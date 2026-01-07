<template>
    <div class="flex flex-col mt-6 shadow-xl rounded-xl overflow-hidden border border-white/5">
        <div class="flex flex-row items-center bg-custom-highlight p-3">
            <div class="shrink-0 border-2 border-white/20 rounded-full overflow-hidden w-10 h-10">
                <Avatar class="w-12 h-12" :image="user_avatar" />
            </div>
            <div class="flex flex-col ml-3">
                <h4 class="font-bold text-white text-lg leading-tight">{{ username }}</h4>
                <h6 class="text-white/70 text-xs">{{ created_at }}</h6>
            </div>
        </div>

        <div class="bg-custom-cards_menu">
            <div class="p-6">
                <p class="text-custom-first_text text-lg leading-relaxed">
                    {{ content }}
                </p>
            </div>

            <div class="px-5 py-3 bg-black/20 border-t border-white/5 flex items-center gap-4">
                <button class="flex items-center gap-2 group transition-all">
                    <div class="p-2 rounded-full group-hover:bg-red-500/20 transition-colors"
                        @click="$emit('likePost')">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-custom-highlight group-hover:text-red-500 transition-colors"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                        </svg>
                    </div>
                    <span class="text-custom-first_text font-bold text-lg group-hover:text-red-500 transition-colors">
                        {{ likes_num }}
                    </span>
                </button>

                <div class="h-4 w-[1px] bg-white/10"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
    defineProps({
        id: Number,
        username: String,
        user_avatar: String,
        created_at: String,
        content: String,
        likes_num: Number,
    })

    defineEmits(['likePost'])
</script>