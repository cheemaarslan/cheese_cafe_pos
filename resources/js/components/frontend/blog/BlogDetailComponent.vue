<template>
    <div class="relative overflow-hidden bg-white min-h-screen">
        <LoadingComponent :props="loading" />
        
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 rounded-full bg-primary/5 blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-1/4 left-0 -ml-32 w-80 h-80 rounded-full bg-primary/5 blur-3xl opacity-50 pointer-events-none"></div>

        <section class="py-12 relative z-10">
            <div class="container mx-auto px-4">
                
                <!-- Not Found State -->
                <div v-if="!loading.isActive && !blog" class="max-w-3xl mx-auto text-center py-20">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-400">
                        <i class="fas fa-file-alt text-4xl"></i>
                    </div>
                    <h2 class="text-3xl font-serif font-bold text-gray-800 mb-4">Article Not Found</h2>
                    <p class="text-gray-500 mb-8">The article you are looking for might have been moved or deleted.</p>
                    <router-link 
                        :to="{ name: 'frontend.blog' }"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white rounded-full font-bold shadow-lg hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300"
                    >
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Journal</span>
                    </router-link>
                </div>

                <!-- Blog Content -->
                <div v-if="blog" class="max-w-4xl mx-auto animate-fade-in-up">
                    
                    <!-- Navigation -->
                    <router-link 
                        :to="{ name: 'frontend.blog' }"
                        class="group inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-primary transition-colors mb-8 uppercase tracking-wider"
                    >
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                        <span>{{ $t('button.back_to_blog') || 'Back to Journal' }}</span>
                    </router-link>

                    <!-- Article Header -->
                    <div class="text-center max-w-3xl mx-auto mb-10">
                        <!-- Category Eyebrow -->
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest rounded-full">
                                {{ blog.category || 'Food & Drink' }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-800 font-serif leading-tight mb-6">
                            {{ blog.title }}
                        </h1>

                        <!-- Meta Data Row -->
                        <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-gray-500 font-medium">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                    <img :src="`https://ui-avatars.com/api/?name=${blog.author || 'Admin'}&background=random`" alt="Author" class="w-full h-full object-cover">
                                </div>
                                <span>{{ blog.author || 'Editorial Team' }}</span>
                            </div>
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                            <div class="flex items-center gap-2">
                                <i class="far fa-clock text-primary"></i>
                                <span>{{ formatDate(blog.created_at) }}</span>
                            </div>
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-fire text-primary"></i>
                                <span>5 min read</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div class="relative w-full aspect-w-16 aspect-h-9 rounded-[2rem] overflow-hidden shadow-2xl mb-12 group">
                        <img 
                            :src="blog.image" 
                            :alt="blog.title" 
                            class="w-full h-[450px] object-cover transform transition-transform duration-1000 group-hover:scale-105 "
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent pointer-events-none"></div>
                        
                        <!-- Floating Date Badge -->
                        <div class="absolute top-6 left-6 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl text-center min-w-[80px] border border-white/20">
                            <span class="block text-3xl font-black text-gray-800 leading-none tracking-tight">
                                {{ getDay(blog.created_at) }}
                            </span>
                            <span class="block text-xs font-bold text-primary uppercase tracking-widest mt-1">
                                {{ getMonth(blog.created_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- Main Content Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                        
                        <!-- Left Sidebar (Share) -->
                        <div class="lg:col-span-1 hidden lg:flex flex-col items-center sticky top-24 h-fit gap-4 mt-8">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 -rotate-90 mb-2 whitespace-nowrap">Share</span>
                            
                            <button @click.prevent="shareOnFacebook" class="w-10 h-10 rounded-full bg-white border border-gray-100 text-gray-500 hover:text-[#1877F2] hover:border-[#1877F2] hover:-translate-y-1 transition-all duration-300 shadow-sm flex items-center justify-center">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button @click.prevent="shareOnTwitter" class="w-10 h-10 rounded-full bg-white border border-gray-100 text-gray-500 hover:text-[#1DA1F2] hover:border-[#1DA1F2] hover:-translate-y-1 transition-all duration-300 shadow-sm flex items-center justify-center">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button @click.prevent="shareOnLinkedIn" class="w-10 h-10 rounded-full bg-white border border-gray-100 text-gray-500 hover:text-[#0A66C2] hover:border-[#0A66C2] hover:-translate-y-1 transition-all duration-300 shadow-sm flex items-center justify-center">
                                <i class="fab fa-linkedin-in"></i>
                            </button>
                            <button @click.prevent="copyLink" class="w-10 h-10 rounded-full bg-white border border-gray-100 text-gray-500 hover:text-primary hover:border-primary hover:-translate-y-1 transition-all duration-300 shadow-sm flex items-center justify-center">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>

                        <!-- Content Body -->
                        <div class="lg:col-span-11">
                            <!-- Lead Text -->
                            <p class="text-xl md:text-2xl font-serif text-gray-700 leading-relaxed italic my-8 border-l-4 border-primary pl-6">
                                {{ blog.description }}
                            </p>

                            <!-- HTML Content -->
                            <div class="prose prose-lg prose-amber max-w-none blog-typography">
                                <div v-if="blog.content" v-html="blog.content"></div>
                                <div v-else class="space-y-6 text-gray-600 leading-8">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                    <h3>The Culinary Art</h3>
                                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                    <ul>
                                        <li>Fresh ingredients sourced daily</li>
                                        <li>Authentic recipes passed down</li>
                                        <li>Passion in every plate</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div v-if="blog.tags && blog.tags.length > 0" class="mt-12 pt-8 border-t border-gray-100">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="text-sm font-bold text-gray-400 uppercase tracking-widest mr-2">Tags:</span>
                                    <span 
                                        v-for="tag in blog.tags" 
                                        :key="tag"
                                        class="px-4 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold uppercase tracking-wide hover:bg-primary hover:text-white transition-all duration-300 cursor-pointer"
                                    >
                                        #{{ tag }}
                                    </span>
                                </div>
                            </div>

                            <!-- Mobile Share (Visible only on small screens) -->
                            <div class="lg:hidden mt-8 pt-8 border-t border-gray-100">
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Share this post</h4>
                                <div class="flex gap-4">
                                    <button @click.prevent="shareOnFacebook" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center"><i class="fab fa-facebook-f"></i></button>
                                    <button @click.prevent="shareOnTwitter" class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center"><i class="fab fa-twitter"></i></button>
                                    <button @click.prevent="shareOnLinkedIn" class="w-10 h-10 rounded-full bg-blue-700 text-white flex items-center justify-center"><i class="fab fa-linkedin-in"></i></button>
                                    <button @click.prevent="copyLink" class="w-10 h-10 rounded-full bg-gray-600 text-white flex items-center justify-center"><i class="fas fa-link"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Posts Section -->
                    <div v-if="relatedBlogs.length > 0" class="mt-24">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-2xl md:text-3xl font-serif font-bold text-gray-800">You Might Also Like</h3>
                            <div class="h-px flex-1 bg-gray-200 ml-8"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div 
                                v-for="relatedBlog in relatedBlogs" 
                                :key="relatedBlog.id"
                                class="group cursor-pointer flex flex-col"
                                @click="viewBlog(relatedBlog)"
                            >
                                <div class="relative h-56 rounded-2xl overflow-hidden mb-4 shadow-md">
                                    <img 
                                        :src="relatedBlog.image" 
                                        :alt="relatedBlog.title" 
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    />
                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
                                </div>
                                <span class="text-xs font-bold text-primary uppercase tracking-widest mb-2 block">
                                    {{ getMonth(relatedBlog.created_at) }} {{ getDay(relatedBlog.created_at) }}
                                </span>
                                <h4 class="text-lg font-bold font-serif text-gray-800 group-hover:text-primary transition-colors leading-snug mb-2">
                                    {{ relatedBlog.title }}
                                </h4>
                                <p class="text-sm text-gray-500 line-clamp-2">
                                    {{ relatedBlog.description }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";

export default {
    name: "BlogDetailComponent",
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            blog: null,
            relatedBlogs: [],
            // Fallback data for demo
            fallbackBlogs: [
                {
                    id: 1,
                    title: "The Secret to the Perfect Sourdough Pizza Crust",
                    slug: "perfect-sourdough",
                    image: "https://images.unsplash.com/photo-1513104890138-7c749659a591?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
                    description: "Discover the fermentation secrets that make our crusts light, airy, and distinctively tangy. It's not just dough; it's science.",
                    content: "<p>Making the perfect sourdough pizza crust is an art that combines science, patience, and a little bit of magic. The key lies in understanding the fermentation process and how it affects the texture and flavor of your dough.</p><h3>Why Sourdough?</h3><p>Start with a healthy, active sourdough starter. This is the foundation of your crust and will give it that distinctive tangy flavor that sets sourdough apart from regular pizza dough. Unlike commercial yeast, wild yeast works slower, breaking down gluten more effectively which makes it easier to digest.</p><blockquote>Good pizza is not just food, it's an emotion.</blockquote><p>When shaping your dough, be gentle. You want to preserve those precious air bubbles you've worked so hard to create during fermentation.</p>",
                    created_at: "2023-10-12",
                    author: "Chef Mario",
                    category: "Recipes",
                    tags: ["Pizza", "Sourdough", "Baking", "Italian"]
                },
                {
                    id: 2,
                    title: "5 Spices That Will Transform Your Home Cooking",
                    slug: "5-spices-transform",
                    image: "https://images.unsplash.com/photo-1596040033229-a9821ebd058d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
                    description: "Elevate your daily meals with these essential spices found in every professional kitchen. Small pinch, big difference.",
                    content: "<p>Professional chefs know that the right spices can transform a simple dish into something extraordinary. Here are five essential spices that should be in every home kitchen.</p>",
                    created_at: "2023-10-05",
                    author: "Sarah J.",
                    category: "Tips & Tricks",
                    tags: ["Spices", "Cooking Tips", "Kitchen Essentials"]
                },
                {
                    id: 3,
                    title: "Hosting the Ultimate Outdoor BBQ Party",
                    slug: "outdoor-bbq-party",
                    image: "https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
                    description: "From layout to playlist, and of course the menu—here is your complete guide to hosting a memorable summer feast.",
                    content: "<p>Summer is the perfect time to gather friends and family for an outdoor BBQ party. With proper planning and preparation, you can create an unforgettable experience.</p>",
                    created_at: "2023-09-28",
                    author: "Event Team",
                    category: "Events",
                    tags: ["BBQ", "Party", "Summer", "Outdoor"]
                }
            ]
        };
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    mounted() {
        this.loadBlog();
    },
    methods: {
        loadBlog() {
            const slug = this.$route.params.slug;
            
            if (!slug) {
                this.blog = null;
                return;
            }

            this.loading.isActive = true;

            // Try to load the specific blog and the list (for related posts) in parallel
            Promise.all([
                this.$store.dispatch("frontendBlog/show", slug),
                this.$store.dispatch("frontendBlog/lists", { vuex: true }).catch(() => null)
            ])
                .then(([showRes]) => {
                    if (showRes && showRes.data && showRes.data.data) {
                        this.blog = showRes.data.data;
                        // normalize image if necessary
                        this.blog.image = this.blog.image || this.blog.preview || this.blog.cover || (this.blog.media && this.blog.media[0] && (this.blog.media[0].preview || this.blog.media[0].full_url || this.blog.media[0].url)) || '/images/default/no-image.png';
                    } else {
                        // fallback: try to find in list or demo data
                        const all = this.$store.getters["frontendBlog/lists"] || [];
                        this.blog = all.find(b => b.slug === slug) || this.fallbackBlogs.find(b => b.slug === slug) || null;
                    }
                    if (this.blog) {
                        this.loadRelatedBlogs();
                    }
                    this.loading.isActive = false;
                })
                .catch(() => {
                    // Fallback to demo data
                    this.blog = this.fallbackBlogs.find(b => b.slug === slug);
                    if (this.blog) {
                        this.loadRelatedBlogs();
                    }
                    this.loading.isActive = false;
                });
        },
        loadRelatedBlogs() {
            try {
                const allBlogs = this.$store.getters["frontendBlog/lists"] || [];
                const source = (allBlogs && allBlogs.length > 0) ? allBlogs : this.fallbackBlogs;
                // normalize images
                const normalized = source.map(b => {
                    const image = b.image || b.preview || b.cover || (b.media && b.media[0] && (b.media[0].preview || b.media[0].full_url || b.media[0].url)) || '/images/default/no-image.png';
                    return Object.assign({}, b, { image });
                });
                this.relatedBlogs = normalized
                    .filter(b => b.id !== this.blog.id)
                    .slice(0, 3);
            } catch (err) {
                this.relatedBlogs = this.fallbackBlogs
                    .filter(b => b.id !== this.blog.id)
                    .slice(0, 3);
            }
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
        formatDate(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            return date.toLocaleDateString('default', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        },
        viewBlog(blog) {
            this.$router.push({ 
                name: 'frontend.blog.item', 
                params: { slug: blog.slug } 
            }).then(() => {
                this.loadBlog();
                window.scrollTo(0, 0);
            });
        },
        shareOnFacebook() {
            const url = window.location.href;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        },
        shareOnTwitter() {
            const url = window.location.href;
            const text = this.blog.title;
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
        },
        shareOnLinkedIn() {
            const url = window.location.href;
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank');
        },
        copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alertService.success(this.$t('message.link_copied') || 'Link copied to clipboard!');
            }).catch(() => {
                alertService.error('Failed to copy link');
            });
        }
    },
    watch: {
        '$route.params.slug': function() {
            this.loadBlog();
            window.scrollTo(0, 0);
        }
    }
};
</script>

<style scoped>
/* Typography Overrides for Blog Content */
.blog-typography h2 {
    @apply text-3xl font-serif font-bold text-gray-800  mt-10 mb-6;
}
.blog-typography h3 {
    @apply text-2xl font-serif font-bold text-gray-800  mt-8 mb-4;
}
.blog-typography p {
    @apply mb-6 leading-8 text-lg text-gray-600 font-light;
}
.blog-typography blockquote {
    @apply border-l-4 border-primary pl-6 italic text-xl text-gray-700 my-8 py-2 bg-gray-50 rounded-r-lg;
}
.blog-typography ul {
    @apply list-disc list-outside ml-6 mb-6 space-y-2 text-gray-600;
}
.blog-typography li::marker {
    @apply text-primary;
}
.blog-typography a {
    @apply text-primary font-bold hover:underline transition-colors;
}

/* Animation Utilities */
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>