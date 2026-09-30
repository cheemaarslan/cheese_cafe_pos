import axios from "axios";

export const frontendContact = {
    namespaced: true,
    actions: {
        send: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post("frontend/contact", payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
};
