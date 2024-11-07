<template>
  <div>
    <Head title="Dashboard" />
    <h1 class="mb-8 text-3xl font-bold">Dashboard</h1>
    <div class="bg-white max-w-md rounded overflow-hidden shadow-lg mb-6">
      <div class="px-6 py-4">
        <h2 class="text-xl font-semibold mb-4">Today Attendance</h2>
        <div class="flex">
          <loading-button :loading="loading_check_in"
            @click="timeIn"
            class="btn-indigo mr-4"
            :class="{'bg-blue-500 text-white font-bold rounded opacity-50 cursor-not-allowed': checkedInToday || loading_check_in}"
            :disabled="checkedInToday">
            {{ checkedInToday ? 'Already Checked In' : 'Check In' }}
          </loading-button>
          <loading-button :loading="loading_check_out" @click="timeOut" class="btn-indigo"
            :class="{'bg-blue-500 text-white font-bold rounded opacity-50 cursor-not-allowed': (!checkedInToday || checkedOutToday || loading_check_out)}"
           :disabled="!checkedInToday || checkedOutToday">
              {{ !checkedInToday ? "You're not Checked In" : (checkedOutToday ? 'Already Checked Out' : 'Check Out') }}
          </loading-button>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <h1 class="pb-4 pt-6 px-6 text-xl font-bold">Previous Attendance</h1>
      <hr>
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Date</th>
          <th class="pb-4 pt-6 px-6">Checked In At</th>
          <th class="pb-4 pt-6 px-6">Checked Out At</th>
        </tr>
        <tr v-for="(item, index) in check_in_data" :key="index" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t items-center px-6 py-4 focus:text-indigo-500">
            {{ item.date }}
          </td>
          <td class="border-t items-center px-6 py-4 focus:text-indigo-500">
            {{ item.time_in }}
          </td>
          <td class="w-px border-t items-center px-6 py-4 focus:text-indigo-500">
            {{ item.time_out }}
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import axios from 'axios';
import { onMounted,ref } from 'vue';

export default {
  props: {
    checkedInToday: Boolean,
    checkedOutToday: Boolean,
    attendance: Array,
  },
  components: {
    Head,
    LoadingButton,
  },
  layout: Layout,

  setup(props) {
    const checkedInToday = ref(false);
    const checkedOutToday = ref(false);
    const check_in_data = ref([]);
    const loading_check_in = ref(false);
    const loading_check_out = ref(false);

    onMounted(() => {
      checkedInToday.value = props.checkedInToday;
      checkedOutToday.value = props.checkedOutToday;
      check_in_data.value = props.attendance;
    });

     // Function to handle Check-In
     const timeIn = async () => {
      try {
        // disable the button while loading

        loading_check_in.value = true;
        const response = await axios.get('/Check-In');
        checkedInToday.value = true; // Set as checked in after successful request
        check_in_data.value.unshift(response.data); // Add new check-in data to the list
        loading_check_in.value = false;
      } catch (error) {
        console.error("Error during check-in:", error);
        alert("Failed to check in. Please try again.");
      }
    };

    // Function to handle Check-Out
    const timeOut = async () => {
      try {
        loading_check_out.value = true;
        await axios.get('/Check-Out');
        checkedOutToday.value = true; // Set as checked out after successful request
        check_in_data.value[0].time_out = new Date().toLocaleTimeString();
        loading_check_out.value = false;
      } catch (error) {
        console.error("Error during check-out:", error);
        alert("Failed to check out. Please try again.");
      }
    };

    return {
      checkedInToday,
      checkedOutToday,
      check_in_data,
      timeIn,
      timeOut,
      loading_check_in,
      loading_check_out,
    }
  },
}
</script>
