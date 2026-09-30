<template>
    <LoadingComponent :props="loading" />
    
    <section class="mb-16 mt-8">
        <div class="container">
            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="capitalize text-[26px] leading-[40px] font-semibold text-primary mb-2">
                    {{ 'Our Blog' }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ 'Discover tasty recipes, food tips, and updates from our restaurant.' }}
                </p>
            </div>

            <!-- Blog Grid -->
            <div v-if="blogs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="(blog, index) in blogs" 
                    :key="blog.id || index"
                    class="group flex flex-col bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 transform hover:-translate-y-1"
                >
                    <!-- Image Container -->
                    <div class="relative h-56 overflow-hidden">
                        <img 
                            :src="blog.image" 
                            :alt="blog.title" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        />
                        
                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-60 group-hover:opacity-40 transition-opacity duration-300"></div>

                        <!-- Date Badge -->
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-3 py-2 rounded-lg shadow-md text-center min-w-[60px]">
                            <span class="block text-xl font-bold text-gray-800 leading-none">
                                {{ getDay(blog.created_at) }}
                            </span>
                            <span class="block text-[10px] font-semibold text-primary uppercase tracking-wider mt-1">
                                {{ getMonth(blog.created_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- Content Container -->
                    <div class="flex-1 p-6 flex flex-col">
                        <!-- Meta Info -->
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                            <div class="flex items-center gap-1">
                                <i class="lab lab-user-circle text-primary"></i>
                                <span>{{ blog.author || 'Admin' }}</span>
                            </div>
                            <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                            <div class="flex items-center gap-1">
                                <i class="lab lab-tag text-primary"></i>
                                <span>{{ blog.category || 'Food' }}</span>
                            </div>
                        </div>

                        <!-- Title -->
                        <a href="#" @click.prevent="viewBlogDetail(blog)">
                            <h3 class="text-lg font-semibold text-heading mb-2 leading-snug group-hover:text-primary transition-colors line-clamp-2">
                                {{ blog.title }}
                            </h3>
                        </a>

                        <!-- Description -->
                        <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3 flex-1">
                            {{ blog.description }}
                        </p>

                        <!-- Read More Link -->
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <a 
                                href="#"
                                @click.prevent="viewBlogDetail(blog)"
                                class="inline-flex items-center text-sm font-medium text-primary hover:text-primary/80 transition-colors"
                            >
                                {{ $t('button.read_more') || 'Read More' }}
                                <i class="lab lab-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="mt-12">
                <div class="max-w-[250px] mx-auto">
                    <img class="w-full mb-8" :src="setting.item_not_found || '/images/default/no-data.png'" alt="no_blogs">
                </div>
                <p class="text-center text-gray-500 mb-4">{{ $t('message.no_blogs_found') || 'No blog posts available' }}</p>
                <router-link 
                    :to="{ name: 'frontend.home' }"
                    class="block w-full mx-auto max-w-[250px] py-3 rounded-3xl capitalize text-base font-medium leading-6 text-center bg-primary text-white hover:bg-primary/90 transition"
                >
                    {{ $t('button.go_home') }}
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "BlogComponent",
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            blogs: []
        };
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    mounted() {
        this.fetchBlogs();
    },
    methods: {
        fetchBlogs() {
            this.loading.isActive = true;
            this.$store.dispatch('frontendBlog/lists', { 
                vuex: true,
                paginate: 0,
                order_column: 'id',
                order_type: 'desc'
            })
                .then(() => {
                    const blogs = this.$store.getters['frontendBlog/lists'] || [];
                    // Ensure image is properly set
                    this.blogs = blogs.map(blog => ({
                        ...blog,
                        image: blog.image || '/images/default/no-image.png'
                    }));
                    this.loading.isActive = false;
                    console.log(this.blogs);
                })
                .catch((err) => {
                    console.error('Error fetching blogs:', err);
                    this.blogs = [];
                    this.loading.isActive = false;
                });
        },
        getDay(dateString) {
            if (!dateString) return "01";
            const date = new Date(dateString);
            return date.getDate().toString().padStart(2, '0');
        },
        getMonth(dateString) {
            if (!dateString) return "JAN";
            const date = new Date(dateString);
            return date.toLocaleString('default', { month: 'short' }).toUpperCase();
        },
        viewBlogDetail(blog) {
            this.$router.push({ 
                name: 'frontend.blog.item', 
                params: { slug: blog.slug } 
            });
        }
    }
};
</script>
