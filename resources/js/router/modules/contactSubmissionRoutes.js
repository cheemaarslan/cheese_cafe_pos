const ContactSubmissionComponent = () => import("../../components/admin/contactSubmissions/ContactSubmissionComponent");
const ContactSubmissionListComponent = () => import("../../components/admin/contactSubmissions/ContactSubmissionListComponent");

export default [
    {
        path: "/admin/contact-submissions",
        component: ContactSubmissionComponent,
        name: "admin.contactSubmissions",
        redirect: { name: "admin.contactSubmissions.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "contact-submissions",
            breadcrumb: "contact_submissions",
        },
        children: [
            {
                path: "",
                component: ContactSubmissionListComponent,
                name: "admin.contactSubmissions.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "contact-submissions",
                    breadcrumb: "",
                },
            },
        ],
    },
];
