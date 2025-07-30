<div class="bg-gray-100 min-h-screen flex items-center justify-center">
  {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ✅ Axios for AJAX requests --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Batch Job Progress</h1>

        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Status:</p>
                <p id="status" class="text-lg font-semibold text-gray-800">Pending...</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 mb-1">Progress:</p>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    <div id="progress-bar" class="bg-blue-600 h-4 transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="text-sm text-gray-600 mt-1">
                    <span id="progress-count">0</span> / <span id="total-count">0</span> jobs
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-600">Failed Jobs:</p>
                <p id="failed-count" class="text-red-600 font-semibold">0</p>
            </div>
        </div>
    </div>

    <script>
        const batchId = '{{ $id }}';
        function fetchStatus() {
            axios.get(`/batch/view/${batchId}`)
                .then(response => {
                    const data = response.data;
                    if (!data) return;
                    console.log("id: ",batchId)
                    console.log("STATUS: ", data)
                    const statusEl = document.getElementById('status');
                    const progressBar = document.getElementById('progress-bar');
                    const processed = data.processedJobs;
                    const total = data.totalJobs;
                    const failed = data.failed_jobs;

                    document.getElementById('progress-count').textContent = processed;
                    document.getElementById('total-count').textContent = total;
                    document.getElementById('failed-count').textContent = failed;

                    const percent = total > 0 ? Math.round((processed / total) * 100) : 0;
                    progressBar.style.width = `${percent}%`;

                    if (data.finished_at) {
                        statusEl.textContent = failed > 0 ? 'Completed with Errors' : 'Completed';
                        statusEl.classList.add(failed > 0 ? 'text-red-600' : 'text-green-600');
                    } else {
                        statusEl.textContent = failed > 0 ? 'Running (with Failures)' : 'Running...';
                        setTimeout(fetchStatus, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    setTimeout(fetchStatus, 3000);
                });
        }

        fetchStatus();
    </script>
</div>
