const InventoryWastageComponent = () => import("../../components/admin/inventoryWastage/InventoryWastageComponent");
const InventoryWastageListComponent = () => import("../../components/admin/inventoryWastage/InventoryWastageListComponent");

export default [
    {
        path: "/admin/inventory-wastage",
        component: InventoryWastageComponent,
        name: "admin.inventory-wastage",
        redirect: { name: "admin.inventory-wastage.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "inventory-wastage",
            breadcrumb: "inventory_wastage",
        },
        children: [
            {
                path: "",
                component: InventoryWastageListComponent,
                name: "admin.inventory-wastage.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "inventory-wastage",
                    breadcrumb: "",
                },
            },
        ],
    },
];
