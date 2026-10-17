import { createRouter, createWebHistory } from "vue-router";
import ENV from '../config/env';
import appService from "../services/appService";
const DashboardComponent = () => import("../components/admin/dashboard/DashboardComponent");
const NotFoundComponent = () => import("../components/frontend/otherPage/NotFoundComponent");
const ExceptionComponent = () => import("../components/frontend/otherPage/ExceptionComponent");
import store from "../store";
import authRoutes from "./modules/authRoutes";
import settingRoutes from "./modules/settingRoutes";
import offerRoutes from "./modules/offerRoutes";
import itemRoutes from "./modules/itemRoutes";
import inventoryRoutes from "./modules/inventoryRoutes";
import expenseRoutes from "./modules/expenseRoutes";
import profitLossRoutes from "./modules/profitLossRoutes";
import couponRoutes from "./modules/couponRoutes";
import onlineOrderRoutes from "./modules/onlineOrderRoutes";
import pushNotificationRoutes from "./modules/pushNotificationRoutes";
import customerRoutes from "./modules/customerRoutes";
import administratorRoutes from "./modules/administratorRoutes";
import deliveryBoyRoutes from "./modules/deliveryBoyRoutes";
import employeeRoutes from "./modules/employeeRoutes";
import frontendRoutes from "./modules/frontendRoutes";
import salesReportRoutes from "./modules/salesReportRoutes";
import itemsReportRoutes from "./modules/itemsReportRoutes";
import posRoutes from "./modules/posRoutes";
import messageRoutes from "./modules/messageRoutes";
import profileRoutes from "./modules/profileRoutes";
import posOrderRoutes from "./modules/posOrderRoutes";
import transactionRoutes from "./modules/transactionRoutes";
import creditBalanceReportRoutes from "./modules/creditBalanceReportRoutes";
import tableOrderRoutes from "./modules/tableOrderRoutes";
import adminTableOrderRoutes from "./modules/adminTableOrderRoutes";
import diningTableRoutes from "./modules/diningTableRoutes";
import subscriberRoutes from "./modules/subscriberRoutes";
import contactSubmissionRoutes from "./modules/contactSubmissionRoutes";
import kitchenDisplaySystemRoutes from "./modules/kitchenDisplaySystemRoutes";
import orderStatusScreenRoutes from "./modules/orderStatusScreenRoutes";
import waiterRoutes from "./modules/waiterRoutes";
import chefRoutes from "./modules/chefRoutes";



const baseRoutes = [
    {
        path: "/",
        name: "root",
        redirect: () => {
            return store.getters.authStatus
                ? { name: "admin.dashboard" }
                : { name: "auth.admin.login" };
        },
    },
    {
        path: "/:pathMatch(.*)*",
        name: "route.notFound",
        component: NotFoundComponent,
        meta: {
            isFrontend: true,
        },
    },
    {
        path: "/exception",
        name: "route.exception",
        component: ExceptionComponent,
    },
    {
        path: "/admin/dashboard",
        component: DashboardComponent,
        name: "admin.dashboard",
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "dashboard",
            breadcrumb: "dashboard",
        },
    },
];

export const routes = baseRoutes.concat(
    frontendRoutes,
    authRoutes,
    settingRoutes,
    offerRoutes,
    itemRoutes,
    inventoryRoutes,
    expenseRoutes,
    profitLossRoutes,
    pushNotificationRoutes,
    couponRoutes,
    onlineOrderRoutes,
    customerRoutes,
    waiterRoutes,
    chefRoutes,
    deliveryBoyRoutes,
    administratorRoutes,
    employeeRoutes,
    salesReportRoutes,
    itemsReportRoutes,
    messageRoutes,
    profileRoutes,
    posRoutes,
    posOrderRoutes,
    transactionRoutes,
    creditBalanceReportRoutes,
    tableOrderRoutes,
    adminTableOrderRoutes,
    diningTableRoutes,
    subscriberRoutes,
    contactSubmissionRoutes,
    kitchenDisplaySystemRoutes,
    orderStatusScreenRoutes
);

const permission = store.getters.authPermission;
appService.recursiveRouter(routes, permission);

const API_URL = ENV.API_URL;
const router = createRouter({
    linkActiveClass: "active",
    mode: 'history',
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { left: 0, top: 0 }
    }
});

// The customer-facing storefront is hidden for now — only the admin login and
// the admin/staff dashboard area should be reachable. Every route that uses the
// storefront theme (isFrontend: true) is a customer page EXCEPT the admin login
// screen itself and the generic error pages, so those three stay allowed.
const ALLOWED_FRONTEND_ROUTE_NAMES = ["auth.admin.login", "route.notFound", "route.exception"];

router.beforeEach((to, from, next) => {
    const isHiddenCustomerRoute = to.meta.isFrontend === true
        && !ALLOWED_FRONTEND_ROUTE_NAMES.includes(to.name);

    if (isHiddenCustomerRoute) {
        next(store.getters.authStatus ? { name: "admin.dashboard" } : { name: "auth.admin.login" });
        return;
    }

    if (to.meta.auth === true) {
        if (!store.getters.authStatus) {
            next({ name: "auth.admin.login" });
        } else {
            if (to.meta.isFrontend === false) {
                if (to.meta.access === false) {
                    next({
                        name: "route.exception",
                    });
                } else {
                    next();
                }
            } else {
                next();
            }
        }
    } else if (to.name === "auth.admin.login" && store.getters.authStatus) {
        next({ name: "admin.dashboard" });
    } else {
        next();
    }
});
export default router;
