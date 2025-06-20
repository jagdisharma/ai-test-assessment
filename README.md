# Smart Ticket Triage & Dashboard

### Laravel 11 (Kernel-less) + Vue 3 SPA

---

## 🚀 Setup Instructions

1. Clone the repo
2. `composer install`
3. `cp .env.example .env && php artisan key:generate`
4. Configure `.env` for DB, OpenAI
5. `php artisan migrate --seed`
6. `npm install && npm run dev`
7. `php artisan queue:work`
8. Access app at: `http://localhost:8000`

---

## 🤖 AI Classification
Set `OPENAI_CLASSIFY_ENABLED=true` to enable real classification using OpenAI.  
Otherwise it returns dummy data.

...
