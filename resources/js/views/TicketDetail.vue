<template>
  <div>
    <TicketDetailComponent :id="id" />
  </div>
</template>

<script>
import TicketDetailComponent from '../components/TicketDetail.vue';

export default {
  name: 'TicketDetail',
   props: ['id'],
  components: {
    TicketDetailComponent
  },
};
</script>
