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

        <button type="submit" class="btn btn--primary">
          <i class="pi pi-send"></i> Submit Ticket
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
  background-color: var(--color-white);
  border-radius: 1.5rem;
  box-shadow: 0 10px 24px var(--color-shadow);
  padding: 2.5rem 2rem;
}

.ticket-form__title {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  color: var(--color-primary);
}

.ticket-form__description {
  font-size: 1.1rem;
  color: var(--color-secondary);
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
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: var(--color-primary);
}

.ticket-form__input,
.ticket-form__textarea {
  padding: 0.75rem 1rem;
  border: 1.5px solid var(--color-secondary);
  border-radius: 0.7rem;
  font-size: 1rem;
  outline: none;
  background: var(--color-accent);
  color: var(--color-primary);
  transition: border-color 0.3s;
}

.ticket-form__input:focus,
.ticket-form__textarea:focus {
  border-color: var(--color-primary);
}

.btn {
  align-self: flex-start;
  background-color: var(--color-primary);
  color: var(--color-white);
  padding: 0.75rem 1.5rem;
  border-radius: 0.7rem;
  font-weight: 700;
  font-size: 1rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s, box-shadow 0.2s;
  box-shadow: 0 2px 8px var(--color-shadow);
}

.btn--primary:hover {
  background-color: var(--color-secondary);
  color: var(--color-white);
}

.ticket-form__error {
  color: #dc2626;
  margin-top: 1rem;
  font-weight: 700;
  background: #fff0f0;
  border-radius: 0.5rem;
  padding: 0.5rem 1rem;
}

.btn i {
  margin-right: 0.4em;
  font-size: 1.1em;
  vertical-align: middle;
}

@media (max-width: 600px) {
  .ticket-form__box {
    padding: 1rem 0.5rem;
    border-radius: 0.7rem;
  }
  .ticket-form__title {
    font-size: 1.3rem;
  }
}
</style>
