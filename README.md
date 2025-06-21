# Smart Ticket Classification System

A Laravel-based ticket management system with AI-powered classification using OpenAI GPT.

## Features

### Ticket Classification
- **Individual Classification**: Click "Classify" button on any unclassified ticket
- **Bulk Classification**: Classify all unclassified tickets at once
- **Real-time Updates**: No page refresh needed - tickets update automatically
- **Queue-based Processing**: Background job processing for better performance
- **Error Handling**: Comprehensive error handling with user feedback

### AI Integration
- **OpenAI GPT-4o**: Advanced AI classification with confidence scores
- **Fallback Mode**: Dummy classification when OpenAI is disabled
- **Configurable**: Easy to enable/disable AI classification

### User Interface
- **Responsive Design**: Works on desktop and mobile devices
- **Loading States**: Spinners and progress indicators
- **Queue Status**: Real-time queue monitoring
- **Filtering**: Search and filter by status, category, and content

## Setup

### 1. Environment Configuration
Add these to your `.env` file:
```env
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_CLASSIFY_ENABLED=true
QUEUE_CONNECTION=database
```

### 2. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 3. Queue Worker
Start the queue worker to process classification jobs:
```bash
php artisan queue:work --daemon
```

### 4. Development Server
```bash
php artisan serve
npm run dev
```

## How It Works

### Classification Process
1. User clicks "Classify" button on an unclassified ticket
2. System queues a classification job
3. Queue worker processes the job using OpenAI GPT-4o
4. AI analyzes ticket content and assigns category (billing/technical/account)
5. Results are saved with confidence score and explanation


## API Endpoints

- `GET /api/tickets` - List tickets with filtering
- `POST /api/tickets/{id}/classify` - Classify individual ticket
- `GET /api/tickets/{id}/classification-status` - Check classification status
- `POST /api/tickets/bulk-classify` - Bulk classify all unclassified tickets
- `GET /api/stats` - Get ticket statistics
- `GET /api/queue-status` - Check queue status

## Troubleshooting

### OpenAI Not Working
1. Check if `OPENAI_API_KEY` is set correctly
2. Verify `OPENAI_CLASSIFY_ENABLED=true`
3. Check logs for API errors: `tail -f storage/logs/laravel.log`

### Queue Not Processing
1. Ensure queue worker is running: `php artisan queue:work --daemon`
2. Check queue status in the UI
3. Monitor failed jobs: `php artisan queue:failed`

### Classification Failing
1. Check OpenAI API quota and billing
2. Verify ticket content is not empty
3. Review error logs for specific issues
