const InventoryComponent = () => import("../../components/admin/inventory/InventoryComponent");
const InventoryListComponent = () => import("../../components/admin/inventory/InventoryListComponent");
const InventoryShowComponent = () => import("../../components/admin/inventory/InventoryShowComponent");

export default [
    {
        path: "/admin/inventory",
        component: InventoryComponent,
        name: "admin.inventory",
        redirect: { name: "admin.inventory.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "inventory",
            breadcrumb: "inventory",
        },
        children: [
            {
                path: "",
                component: InventoryListComponent,
                name: "admin.inventory.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "inventory",
                    breadcrumb: "",
                },
            },
            {
                path: "show/:id",
                component: InventoryShowComponent,
                name: "admin.inventory.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "inventory",
                    breadcrumb: "view",
                },
            },
        ],
    },
];
