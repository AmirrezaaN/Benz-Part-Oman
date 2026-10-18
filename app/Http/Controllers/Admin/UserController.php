<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim(Identifier::normalizeDigits((string) $request->query('q')));

        $users = User::query()->with('role')->withCount(['orders', 'inquiries'])
            ->when($term !== '', function ($q) use ($term) {
                $q->where(fn ($q) => $q->where('username', 'like', "%{$term}%")
                    ->orWhere('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('internal_code', $term));
            })
            ->when($request->query('role'), fn ($q, $v) => $q->where('role_id', $v))
            ->when($request->query('status') === 'banned', fn ($q) => $q->whereNotNull('banned_at'))
            ->when($request->query('status') === 'staff', fn ($q) => $q->whereHas('role', fn ($r) => $r->where('name', '!=', Role::CUSTOMER)))
            ->latest('id')->paginate(25)->withQueryString();

        return view('admin.users.index', ['users' => $users, 'roles' => Role::query()->orderBy('id')->get()]);
    }

    public function show(Request $request, User $user): View
    {
        $actor = $request->user();

        return view('admin.users.show', [
            'user' => $user->load('role'),
            'orders' => $user->orders()->latest('id')->take(20)->get(),
            'inquiries' => $user->inquiries()->latest('id')->take(20)->get(),
            'assignableRoles' => Role::query()->orderBy('id')->get()->filter(fn (Role $r) => $actor->canAssignRole($r)),
            'canManage' => $this->canManage($actor, $user),
            'activity' => $actor->isSuperAdmin() ? ActivityLog::query()->where('user_id', $user->id)->latest('id')->take(15)->get() : collect(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($this->canManage($actor, $user), 403, 'امکان تغییر نقش این کاربر را ندارید.');

        $role = Role::query()->findOrFail($request->validate(['role_id' => ['required', 'integer']])['role_id']);
        abort_unless($actor->canAssignRole($role), 403, 'شما اجازه اعطای این نقش را ندارید.');

        $old = $user->role?->label;
        $user->role_id = $role->id;
        $user->save();

        // Force the user to re-login so the new permissions apply everywhere
        if ($old !== $role->label) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        ActivityLog::record('user.role_changed', $user, "تغییر نقش {$user->username}: {$old} ← {$role->label}");

        return back()->with('success', 'نقش کاربر به «'.$role->label.'» تغییر کرد.');
    }

    public function toggleBan(Request $request, User $user): RedirectResponse
    {
        abort_unless($this->canManage($request->user(), $user), 403, 'امکان مسدودسازی این کاربر را ندارید.');

        if ($user->isBanned()) {
            $user->banned_at = null;
            $user->ban_reason = null;
            $user->save();
            ActivityLog::record('user.unbanned', $user, 'رفع مسدودی '.$user->username);

            return back()->with('success', 'حساب کاربر فعال شد.');
        }

        $reason = $request->validate(['ban_reason' => ['nullable', 'string', 'max:255']])['ban_reason'] ?? null;
        $user->banned_at = now();
        $user->ban_reason = $reason;
        $user->save();
        DB::table('sessions')->where('user_id', $user->id)->delete();
        ActivityLog::record('user.banned', $user, 'مسدودسازی '.$user->username.($reason ? ' - '.$reason : ''));

        return back()->with('success', 'حساب کاربر مسدود شد.');
    }

    /** Nobody can manage themselves or the super admin; non-owners cannot manage staff with more power than themselves. */
    private function canManage(User $actor, User $target): bool
    {
        if ($actor->is($target) || $target->isSuperAdmin()) {
            return false;
        }

        return $actor->isSuperAdmin() || ! $target->role || $actor->canAssignRole($target->role);
    }
}
