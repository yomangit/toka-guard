<div class="flex items-start max-md:flex-col  z-30 ">
    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="flex w-full flex-1 flex-col gap-4 rounded-xl 
             h-[calc(100vh-16rem)] 
            sm:max-h-[calc(100vh-10rem)] 
            md:max-h-[calc(100vh-11rem)] 
            lg:h-[23rem] 
            2xl:h-[45rem]">
            <div class="h-full flex-1 overflow-y-auto overflow-x-hidden rounded-xl border border-neutral-200 dark:border-base-200 p-4">
                <div class="w-full max-w-full ">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
