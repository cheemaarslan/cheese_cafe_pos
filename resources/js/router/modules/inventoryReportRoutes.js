const InventoryReportComponent = () => import("../../components/admin/inventoryReport/InventoryReportComponent");
const InventoryReportListComponent = () => import("../../components/admin/inventoryReport/InventoryReportListComponent");
const InventoryReportDailyComponent = () => import("../../components/admin/inventoryReport/InventoryReportDailyComponent");

export default [
    {
        path: "/admin/inventory-report",
        component: InventoryReportComponent,
        name: "admin.inventory-report",
        redirect: { name: "admin.inventory-report.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "inventory-report",
            breadcrumb: "inventory_report",
        },
        children: [
            {
                path: "",
                component: InventoryReportListComponent,
                name: "admin.inventory-report.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "inventory-report",
                    breadcrumb: "",
                },
            },
            {
                path: "daily/:id",
                component: InventoryReportDailyComponent,
                name: "admin.inventory-report.daily",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "inventory-report",
                    breadcrumb: "daily_breakdown",
                },
            },
        ],
    },
];
