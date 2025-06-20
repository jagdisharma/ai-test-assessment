<template>
  <div class="ticket-form__container">
    <div class="ticket-form__box">
      <h2 class="ticket-form__title">Need Help? Submit a Support Ticket</h2>
      <p class="ticket-form__description">
        Our support team will get back to you as soon as possible. Please provide a detailed description of your issue.
      </p>

      <form @submit.prevent="submitTicket" class="ticket-form__form">
        <div class="ticket-form__field">
          <label class="ticket-form__label">Subject</label>
          <input
            type="text"
            v-model="form.subject"
            class="ticket-form__input"
            placeholder="Enter a short title for your issue"
            required
          />
        </div>

        <div class="ticket-form__field">
          <label class="ticket-form__label">Description</label>
          <textarea
            v-model="form.body"
            class="ticket-form__textarea"
            rows="6"
            placeholder="Describe the problem you're facing in detail..."
            required
          ></textarea>
        </div>

        <button type="submit" class="ticket-form__submit">
          📩 Submit Ticket
        </button>

        <p v-if="errorMessage" class="ticket-form__error">
          {{ errorMessage }}
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'TicketForm',
  data() {
    return {
      form: {
        subject: '',
        body: '',
      },
      errorMessage: '',
    }
  },
  methods: {
    async submitTicket() {
      try {
        await axios.post('/api/tickets', this.form)
        this.$router.push('/ticket-success')
      } catch (error) {
        console.error('Error:', error)
        this.errorMessage =
          error.response?.data?.message || 'Something went wrong. Please try again.'
      }
    },
  },
}
</script>

<style scoped>
.ticket-form__container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem 1rem;
}

.ticket-form__box {
  max-width: 600px;
  width: 100%;
  background-color: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
  padding: 2rem;
}

.ticket-form__title {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #1e3a8a;
}

.ticket-form__description {
  font-size: 1rem;
  color: #555;
  margin-bottom: 1.5rem;
}

.ticket-form__form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.ticket-form__field {
  display: flex;
  flex-direction: column;
}

.ticket-form__label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #333;
}

.ticket-form__input,
.ticket-form__textarea {
  padding: 0.75rem 1rem;
  border: 1px solid #cbd5e0;
  border-radius: 0.5rem;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s;
}

.ticket-form__input:focus,
.ticket-form__textarea:focus {
  border-color: #3b82f6;
}

.ticket-form__submit {
  align-self: flex-start;
  background-color: #2563eb;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 1rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.ticket-form__submit:hover {
  background-color: #1d4ed8;
}

.ticket-form__success {
  margin-top: 1rem;
  color: #16a34a;
  font-weight: 600;
}

.ticket-form__error {
  color: #dc2626;
  margin-top: 1rem;
  font-weight: 600;
}
</style>
