<template>
    <section class="py-10 bg-white relative overflow-hidden" v-if="blogs.length > 0">
        <LoadingComponent :props="loading" />

        <!-- Background Decorations (Subtle Blobs) -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-amber-100 rounded-full blur-3xl opacity-30 pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-100 rounded-full blur-3xl opacity-30 pointer-events-none translate-x-1/2 translate-y-1/2"></div>

        <div class="container mx-auto px-4 relative z-10">
            <!-- Header Row -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">

                <!-- Text + Title -->
                <div class="text-center md:text-left">
                    <h4 class="text-primary/80 font-bold tracking-widest uppercase text-xs md:text-sm mb-2">
                        Latest Blogs
                    </h4>

                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 font-serif">
                        Our <span class="text-primary">Blogs</span>
                    </h2>

                    <div class="w-20 h-1.5 bg-primary mt-3 rounded-full md:mx-0 mx-auto"></div>
                </div>

                <!-- View All Button -->
                <router-link :to="{ name: 'frontend.blog' }" class="rounded-3xl capitalize text-sm leading-6 font-medium py-2 px-4 transition
                       text-primary bg-[#FFEDF4] hover:text-white hover:bg-primary md:self-end self-center">
                    {{ $t("button.view_all") }}
                </router-link>

            </div>

            <!-- Blog Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="blog in blogs" 
                    :key="blog.id"
                    class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col"
                >
                    <!-- Image Container -->
                    <div class="relative h-64 overflow-hidden">
                        <img 
                            :src="blog.image" 
                            :alt="blog.title"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60 group-hover:opacity-40 transition-opacity duration-300"></div>

                        <!-- Date Badge (Floating) -->
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-3 py-2 rounded-xl shadow-md text-center min-w-[60px]">
                            <span class="block text-xl font-extrabold text-gray-800 leading-none">
                                {{ getDay(blog.created_at) }}
                            </span>
                            <span class="block text-[10px] font-bold text-primary uppercase tracking-wider mt-1">
                                {{ getMonth(blog.created_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- Content Container -->
                    <div class="flex-1 p-8 flex flex-col">
                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 text-xs font-medium text-gray-500 mb-4">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-user text-primary text-sm"></i>
                                <span>{{ blog.author || 'Admin' }}</span>
                            </div>
                            <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-tag text-primary text-sm"></i>
                                <span>{{ blog.category || 'Food' }}</span>
                            </div>
                        </div>

                        <!-- Title -->
                        <a href="#" @click.prevent="handleBlogClick(blog)">
                            <h3 class="text-xl font-bold text-gray-800 font-serif mb-3 leading-snug group-hover:text-primary transition-colors line-clamp-2">
                                {{ blog.title }}
                            </h3>
                        </a>

                        <!-- Description (Truncated) -->
                        <p class="text-gray-500 text-sm leading-relaxed mb-6 line-clamp-3 flex-1">
                            {{ blog.description }}
                        </p>

                        <!-- Footer / Link -->
                        <div class="mt-auto border-t border-gray-100 pt-6">
                            <a 
                                href="#"
                                @click.prevent="handleBlogClick(blog)"
                                class="inline-flex items-center text-sm font-bold text-gray-800 group-hover:text-primary transition-colors uppercase tracking-wide"
                            >
                                Read More 
                                <i class="fas fa-long-arrow-alt-right ml-2 transform group-hover:translate-x-2 transition-transform duration-300 text-primary"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "BlogListComponent",
    components: {
        LoadingComponent
    },
    props: {
        limit: {
            type: Number,
            default: 3
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            blogsLocal: [],
        };
    },
    computed: {
        blogs() {
            return this.blogsLocal;
        },
    },
    mounted() {
        this.fetchBlogs();
    },
    methods: {
        fetchBlogs() {
            this.loading.isActive = true;
            // console.log('BlogListComponent: Fetching blogs...');
            
            this.$store.dispatch("frontendBlog/lists", {
                paginate: 0,
                order_column: "id",
                order_type: "desc",
                vuex: true
            })
            .then((res) => {
                // console.log('BlogListComponent: API Response:', res);
                
                // Get blogs from store
                const blogs = this.$store.getters["frontendBlog/lists"] || [];
                // console.log('BlogListComponent: Blogs from store:', blogs);
                
                // Normalize image URLs
                const normalized = blogs.map(blog => {
                    const image = blog.image || '/images/default/no-image.png';
                    return { ...blog, image };
                });
                
                // console.log('BlogListComponent: Normalized blogs:', normalized);
                
                // Limit the results
                this.blogsLocal = normalized.slice(0, this.limit);
                // console.log('BlogListComponent: Final blogs to display:', this.blogsLocal);
                
                this.loading.isActive = false;
            })
            .catch((err) => {
                console.error('BlogListComponent: Error loading blogs:', err);
                console.error('BlogListComponent: Error details:', err.response);
                this.blogsLocal = [];
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
            return date.toLocaleString('default', { month: 'short' });
        },
        handleViewAll() {
            this.$router.push({ name: 'frontend.blog' });
        },
        handleBlogClick(blog) {
            this.$router.push({ 
                name: 'frontend.blog.item', 
                params: { slug: blog.slug } 
            });
        }
    },
};
</script>