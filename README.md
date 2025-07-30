Here’s a polished **README.md** template for your GitHub repository, *laravel-job-batching*, clearly explaining what you built, how it works, and how to set it up:

---

# 📦 Laravel Job Batching - CSV Import & Progress Monitoring

A Laravel-based example that demonstrates how to:

* Upload a CSV file via web form
* Process it in **chunked background jobs** using Laravel 8+ batch queues (`Bus::batch`)
* Track batch progress via JSON endpoints (AJAX polling or API)
* Optionally store the last batch ID in session for later retrieval

---

## 🧩 Features

* **CSV Upload** via form with validation
* **Chunked processing** (e.g. 1,000 rows per job) for efficient queue handling
* **Job Batching** with `Bus::batch`:

  * `then(...)` callback for completion
  * Optional `catch(...)` / `finally(...)` callbacks
* **Real-time progress** reporting via JSON route
* **Session storage** of batch ID for "View Last Upload" link
* Styled UI using Tailwind CSS and basic AJAX with Axios (optional)

---

## 🔧 Installation & Setup

1. Clone the repo:

   ```bash
   git clone https://github.com/mrgonzales-dev/laravel-job-batching.git
   cd laravel-job-batching
   ```

2. Install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Configure `.env` (database, queue driver). Use `database` or `redis`.

4. Run migrations:

   ```bash
   php artisan queue:batches-table
   php artisan make:queue-batches-table
   php artisan migrate
   ```

5. Start the queue worker:

   ```bash
   php artisan queue:work
   ```

6. Serve the app:

   ```bash
   php artisan serve
   ```

---

## 🧠 How It Works

### ✅ File upload & batching (`SalesController@upload`)

* The CSV file is validated and split into chunks.
* Each chunk is wrapped in a `SalesCsvProcess` Job.
* All jobs are dispatched as a single batch via `Bus::batch`.
* Batch ID is stored in session and redirect sent to progress page.

### 📡 Progress endpoint (`SalesController@batch`)

Handles both view and AJAX:

* If the request expects JSON (`wantsJson()`), returns:

  ```json
  {
    "progress": 35,
    "processedJobs": 18,
    "totalJobs": 50,
    "failedJobs": 2,
    "pendingJobs": 30,
    "status": "running",
    "finished_at": null
  }
  ```
* Otherwise, renders `batch-progress.blade.php` view.

### 📄 Blade View (`batch-progress.blade.php`)

* Accepts `id`, `progress`, `processedJobs`, `totalJobs`, `status`, `failedJobs`, `pendingJobs`, `finished_at`
* Renders a progress bar and job status
* Optionally uses Axios polling to refresh JSON every second

### 🧾 Storing last batch ID

* `session()->put('last_batch_id', $batchId)` saves the last batch ID per user session
* A link like **“View Last Upload”** can retrieve this batch for status display

---

## 📁 Example File Structure

```
app/
  Controller/
    SalesController.php
  Jobs/
    SalesCsvProcess.php  // implements ShouldQueue, Batchable
resources/views/
  upload-file.blade.php
  batch-progress.blade.php
routes/web.php
```

---

## 🚀 Getting Started

1. Visit `/` to upload a CSV.
2. After upload, you're redirected to `/batch?id={uuid}`.
3. Page polls `/batch?id={uuid}` endpoint for JSON status updates (if using JS) or uses meta refresh.
4. Optionally visit `/last` to jump back to the most recent batch.

---

## 🧩 Why This Helps

| Problem                                | Solution                                                        |
| -------------------------------------- | --------------------------------------------------------------- |
| Uploading large CSV blocks UI          | Offload work via queued batch jobs                              |
| Want progress % without front-end code | JSON API endpoint returns real-time metrics                     |
| Need to revisit last batch             | Session-based batch ID tracking for convenience                 |
| Process breaking memory limits         | Chunked jobs prevent memory exhaustion and long-running scripts |

---

## 🤝 Reference & Learning

* Laravel’s official job batching docs (`Bus::batch`) ([laravel.com][1], [youtube.com][2], [themsaid.com][3], [dev.to][4], [medium.com][5], [riseuplabs.com][6])
* Community tutorials on queue batching and progress APIs ([themsaid.com][3], [dev.to][7])

---

## ⚙️ Customization Ideas

* Integrate with **Livewire** or **Alpine.js** for improved UI
* Use **Laravel Echo** for real-time broadcasting of batch progress
* Save batch jobs in database for historical tracking
* Enable retrying failed jobs via `queue:retry-batch` command
* Add `allowFailures()` to continue despite failure

---

## 📞 Feedback & Contributions

If you find issues or ideas to improve, feel free to submit an **issue** or **pull request**.
Happy batching 🛠️🎯

[1]: https://laravel.com/docs/12.x/queues?utm_source=chatgpt.com "Queues - Laravel 12.x - The PHP Framework For Web ..."
[2]: https://www.youtube.com/watch?v=WI6jenTRizA&utm_source=chatgpt.com "8. Laravel Job Batching | Upload million records"
[3]: https://themsaid.com/queue-job-batching-in-laravel-how-it-works?utm_source=chatgpt.com "Job batching in Laravel: How it works - Themsaid.com"
[4]: https://dev.to/msnmongare/-how-to-implement-queue-in-laravel-9-4gjd?utm_source=chatgpt.com "How to Implement Queue in Laravel 9"
[5]: https://medium.com/%40harrisrafto/manage-job-batches-efficiently-with-bus-batch-in-laravel-aed0edba7a74?utm_source=chatgpt.com "Manage job batches efficiently with Bus::batch in Laravel"
[6]: https://riseuplabs.com/exporting-large-csv-data-sets-in-laravel-with-job-queue/?utm_source=chatgpt.com "Exporting Large CSV Data Sets in Laravel with Job Queue"
[7]: https://dev.to/ayowandeapp/job-batching-in-laravel-and-vue-4hgb?utm_source=chatgpt.com "Job Batching in Laravel and Vue"
