<template>
  <div>
    <Head title="Dashboard" />
    <h1 class="mb-8 text-3xl font-bold">Dashboard</h1>
    <div class="flex mb-4">
      <div class="w-1/3">
        <div class="bg-white max-w-sm rounded overflow-hidden shadow-lg mb-6">
          <div class="px-6 py-4 text-center">
            <dt class="text-base/7 text-gray-600">Todays Total Check-Ins</dt>
            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{today_check_ins}}</dd>
          </div>
        </div>
      </div>
      <div class="w-1/3">
        <div class="bg-white max-w-sm rounded overflow-hidden shadow-lg mb-6">
      <div class="px-6 py-4 text-center">
        <dt class="text-base/7 text-gray-600">Todays Total Check-Outs</dt>
            <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{today_check_outs}}</dd>
      </div>
    </div>
      </div>
      <div class="w-1/3">
        <div class="bg-white max-w-sm rounded overflow-hidden shadow-lg mb-6">
      <div class="px-6 py-4 text-center">
        <dt class="text-base/7 text-gray-600">Todays Total Not Checked-Ins</dt>
        <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{today_not_check_ins}}</dd>
      </div>
    </div>
      </div>
    </div>
    <div class="flex mb-4">
      <div class="w-1/2 pr-2">
        <div class="bg-white rounded-md shadow overflow-x-auto">
          <h1 class="pb-4 pt-6 px-6 text-xl font-bold">Latest Check-Ins</h1>
          <hr>
          <table class="w-full whitespace-nowrap">
            <tr class="text-left font-bold">
              <th class="pb-4 pt-6 px-6">Name</th>
              <th class="pb-4 pt-6 px-6">Checked In At</th>
              <th class="pb-4 pt-6 px-6">Checked Out At</th>
            </tr>
            <tr v-for="(item, index) in check_in_data" :key="index" class="hover:bg-gray-100 focus-within:bg-gray-100">
              <td class="border-t items-center px-6 py-4 focus:text-indigo-500">
                {{ item.name }}
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
      <div class="w-1/2 pl-2">
        <div class="bg-white rounded-md shadow overflow-x-auto">
          <h1 class="pb-4 pt-6 px-6 text-xl font-bold">Latest Check-Outs</h1>
          <hr>
          <table class="w-full whitespace-nowrap">
            <tr class="text-left font-bold">
              <th class="pb-4 pt-6 px-6">Name</th>
              <th class="pb-4 pt-6 px-6">Checked In At</th>
              <th class="pb-4 pt-6 px-6">Checked Out At</th>
            </tr>
            <tr v-for="(item, index) in check_out_data" :key="index" class="hover:bg-gray-100 focus-within:bg-gray-100">
              <td class="border-t items-center px-6 py-4 focus:text-indigo-500">
                {{ item.name }}
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
    </div>

  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { onMounted,ref } from 'vue';
export default {
  props: {
    attendanceTimeIn: Array,
    attendanceTimeOut: Array,
    notCheckedInCount: Number,
  },
  components: {
    Head,
  },
  layout: Layout,

  setup(props) {

    const check_in_data = ref([]);
    const check_out_data = ref([]);
    const today_check_ins = ref(0);
    const today_check_outs = ref(0);
    const today_not_check_ins = ref(0);

    onMounted(() => {
      console.log(props.attendanceTimeIn);
      check_in_data.value = props.attendanceTimeIn;
      check_out_data.value = props.attendanceTimeOut;
      today_check_ins.value = props.attendanceTimeIn.length;
      today_check_outs.value = props.attendanceTimeOut.length;
      today_not_check_ins.value = props.notCheckedInCount;

      window.Pusher = Pusher;
      window.Echo = new Echo({
          broadcaster: 'reverb',
          key: import.meta.env.VITE_REVERB_APP_KEY,
          wsHost: import.meta.env.VITE_REVERB_HOST,
          wsPort: import.meta.env.VITE_REVERB_PORT,
          wssPort: import.meta.env.VITE_REVERB_PORT,
          forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
          enabledTransports: ['ws', 'wss'],
      });

      window.Echo.channel('owner').listen('.check', (e) => {
        if(e.check_in_type === 'check-out') {
          const index = check_in_data.value.findIndex((item) => item.id === e.id);
          if(index !== -1) {
            check_in_data.value[index].time_out = e.time_out;
          }
          check_out_data.value.unshift(e);
          today_check_outs.value++;
          return;
        }
        check_in_data.value.unshift(e);
        today_check_ins.value++;
        today_not_check_ins.value--;
      });
    });

    return {
      check_in_data,
      check_out_data,
      today_check_ins,
      today_check_outs,
      today_not_check_ins
    }
  },
}
</script>
