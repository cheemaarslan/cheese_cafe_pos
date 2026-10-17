const ProfitLossComponent = () => import("../../components/admin/profitLoss/ProfitLossComponent");
const ProfitLossReportComponent = () => import("../../components/admin/profitLoss/ProfitLossReportComponent");

export default [
    {
        path: "/admin/profit-loss",
        component: ProfitLossComponent,
        name: "admin.profit-loss",
        redirect: { name: "admin.profit-loss.report" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "profit-loss",
            breadcrumb: "profit_loss",
        },
        children: [
            {
                path: "",
                component: ProfitLossReportComponent,
                name: "admin.profit-loss.report",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "profit-loss",
                    breadcrumb: "",
                },
            },
        ],
    },
];
