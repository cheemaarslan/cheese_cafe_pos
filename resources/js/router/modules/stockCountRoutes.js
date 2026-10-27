const StockCountComponent = () => import("../../components/admin/stockCounts/StockCountComponent");
const StockCountListComponent = () => import("../../components/admin/stockCounts/StockCountListComponent");
const StockCountCreateComponent = () => import("../../components/admin/stockCounts/StockCountCreateComponent");
const StockCountShowComponent = () => import("../../components/admin/stockCounts/StockCountShowComponent");

export default [
    {
        path: "/admin/stock-counts",
        component: StockCountComponent,
        name: "admin.stock-counts",
        redirect: { name: "admin.stock-counts.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "stock-counts",
            breadcrumb: "stock_counts",
        },
        children: [
            {
                path: "",
                component: StockCountListComponent,
                name: "admin.stock-counts.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "stock-counts",
                    breadcrumb: "",
                },
            },
            {
                path: "create",
                component: StockCountCreateComponent,
                name: "admin.stock-counts.create",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "stock-counts",
                    breadcrumb: "new_stock_count",
                },
            },
            {
                path: "show/:id",
                component: StockCountShowComponent,
                name: "admin.stock-counts.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "stock-counts",
                    breadcrumb: "view",
                },
            },
        ],
    },
];
