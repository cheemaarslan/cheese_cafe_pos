<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.blogs") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">

                        <!-- Title -->
                        <div class="form-col-12 sm:form-col-6">
                            <label for="title" class="db-field-title required">{{ $t("label.title") }}</label>
                            <input v-model="props.form.title" :class="errors.title ? 'invalid' : ''" type="text"
                                id="title" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.title">{{ errors.title[0] }}</small>
                        </div>

                        <!-- Category -->
                        <div class="form-col-12 sm:form-col-6">
                            <label for="category" class="db-field-title">Category</label>
                            <input v-model="props.form.category" :class="errors.category ? 'invalid' : ''" type="text" id="category"
                                class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.category">{{ errors.category[0] }}</small>
                        </div>

                        <!-- Author
                        <div class="form-col-12 sm:form-col-6">
                            <label for="author" class="db-field-title required">Author</label>
                            <input v-model="props.form.author" :class="errors.author ? 'invalid' : ''" type="text"
                                id="author" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.author">{{ errors.author[0] }}</small>
                        </div> -->

                        <!-- Category
                        <div class="form-col-12 sm:form-col-6">
                            <label for="category" class="db-field-title required">Category</label>
                            <input v-model="props.form.category" :class="errors.category ? 'invalid' : ''" type="text"
                                id="category" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.category">{{ errors.category[0] }}</small>
                        </div> -->

                        <!-- Status -->
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">

                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active"
                                            type="radio" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t("label.active") }}</label>
                                </div>

                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status"
                                            id="inactive" type="radio" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t("label.inactive") }}</label>
                                </div>

                            </div>
                            <small class="db-field-alert" v-if="errors.status">{{ errors.status[0] }}</small>
                        </div>

                        <!-- Image Upload -->
                        <div class="form-col-12 sm:form-col-6">
                            <label for="image" class="db-field-title">Image</label>
                            <input @change="changeImage" :class="errors.image ? 'invalid' : ''" id="image" type="file"
                                class="db-field-control" ref="imageProperty"
                                accept="image/png, image/jpeg, image/jpg, image/webp" />
                            <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
                        </div>

                        <!-- Description -->
                        <div class="form-col-12">
                            <label for="description" class="db-field-title required">{{ $t("label.description")
                                }}</label>
                            <textarea v-model="props.form.description" name="description" class="w-full textarea-border-radius db-field-control" rows="3"></textarea>
                            <small class="db-field-alert" v-if="errors.description">{{ errors.description[0] }}</small>
                        </div>

                        <!-- Content (Full blog body) -->
                        <div class="form-col-12">
                            <label for="content" class="db-field-title">Content</label>
                            <quill-editor v-model:value="props.form.content" id="content"
                                class="!h-52 textarea-border-radius ql-container ql-snow" />
                            <small class="db-field-alert" v-if="errors.content">{{ errors.content[0] }}</small>
                        </div>

                        <!-- Buttons -->
                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-close"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-save"></i>
                                    <span>{{ $t("button.save") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
<script>
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent";
import LoadingComponent from "../../components/LoadingComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import { quillEditor } from 'vue3-quill'

export default {
    name: "BlogCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent, quillEditor },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            image: "",
            errors: {},
        };
    },
    mounted() {
        this.$store.dispatch("menuTemplate/lists");
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_blogs') };
        },
        menuTemplates: function () {
            return this.$store.getters["menuTemplate/lists"];
        },
    },
    methods: {
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        reset: function () {
            appService.modalHide();
            this.$store.dispatch("blog/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                title: "",
                category: "",
                description: "",
                content: "",
                menu_section_id: null,
                template_id: null,
                status: statusEnum.ACTIVE,
            };
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append("title", this.props.form.title);
                const slug = this.props.form.title
                    ? this.props.form.title.toString().toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '')
                    : "";
                fd.append("slug", slug);
                fd.append("status", this.props.form.status);
                fd.append("description", this.props.form.description);
                fd.append("content", this.props.form.content ? this.props.form.content : "");
                fd.append("category", this.props.form.category ? this.props.form.category : "");
                fd.append(
                    "menu_section_id",
                    this.props.form.menu_section_id === null
                        ? ""
                        : this.props.form.menu_section_id
                );
                fd.append(
                    "template_id",
                    this.props.form.template_id === null
                        ? 0
                        : this.props.form.template_id
                );
                if (this.image) {
                    fd.append("image", this.image);
                }
                const tempId = this.$store.getters["blog/temp"].temp_id;
                this.loading.isActive = true;
                this.$store
                    .dispatch("blog/save", {
                        form: fd,
                        search: this.props.search,
                    })
                    .then((res) => {
                        appService.modalHide();
                        this.loading.isActive = false;
                        alertService.successFlip(
                            tempId === null ? 0 : 1,
                            this.$t("menu.blogs")
                        );
                        this.props.form = {
                            title: "",
                            category: "",
                            description: "",
                            content: "",
                            menu_section_id: null,
                            template_id: null,
                            status: statusEnum.ACTIVE,
                        };
                        this.image = "";
                        this.errors = {};
                        this.$refs.imageProperty.value = null;
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response && err.response.data && err.response.data.errors ? err.response.data.errors : {};
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
};
</script>
