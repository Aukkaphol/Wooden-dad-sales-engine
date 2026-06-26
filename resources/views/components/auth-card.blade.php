<section class="mx-auto flex min-h-[calc(100vh-73px)] max-w-6xl items-center px-6 py-12">
    <div class="grid w-full gap-10 lg:grid-cols-[1fr_420px] lg:items-center">
        <div class="max-w-xl">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Enterprise AI Marketing Platform</p>
            <h1 class="mt-4 text-4xl font-semibold text-white sm:text-5xl">
                Build campaigns with brand-safe AI operations.
            </h1>
            <p class="mt-5 text-base leading-7 text-zinc-300">
                Secure access is the first foundation for multi-workspace marketing teams, asset libraries, and generation workflows.
            </p>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/[0.03] p-8 shadow-2xl shadow-black/30">
            {{ $slot }}
        </div>
    </div>
</section>
