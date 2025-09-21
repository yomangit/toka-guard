<div class="flex items-start max-md:flex-col ">
    <div class="flex-1 self-stretch max-md:pt-4">
        <div class="flex w-full flex-1 flex-col gap-4 rounded-xl 
            max-h-[calc(100vh-14rem)] 
            sm:max-h-[calc(100vh-9rem)] 
            md:max-h-[calc(100vh-14rem)] 
            lg:max-h-[calc(100vh-16rem)] 
            2xl:max-h-[41rem]">
            <div class="h-full flex-1 overflow-y-auto overflow-x-hidden inset-shadow-sm rounded-xl border border-neutral-200 dark:border-base-200 px-4 py-2">
                <div class="w-full max-w-full ">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
