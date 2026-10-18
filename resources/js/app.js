import './bootstrap';
import { initUi } from './modules/ui';
import { initCurrency } from './modules/currency';
import { initSliders } from './modules/sliders';
import { initSearch } from './modules/search';
import { initVehicleFinder } from './modules/vehicle-finder';
import { initCart } from './modules/cart';
import { initOtp } from './modules/otp';

document.addEventListener('DOMContentLoaded', () => {
    initUi();
    initCurrency();
    initSliders();
    initSearch();
    initVehicleFinder();
    initCart();
    initOtp();
});
