const ExpenseComponent = () => import("../../components/admin/expenses/ExpenseComponent");
const ExpenseListComponent = () => import("../../components/admin/expenses/ExpenseListComponent");

export default [
    {
        path: "/admin/expenses",
        component: ExpenseComponent,
        name: "admin.expenses",
        redirect: { name: "admin.expenses.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "expenses",
            breadcrumb: "expenses",
        },
        children: [
            {
                path: "",
                component: ExpenseListComponent,
                name: "admin.expenses.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "expenses",
                    breadcrumb: "",
                },
            },
        ],
    },
];
