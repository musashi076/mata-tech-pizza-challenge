<template>
  <q-page padding>
    <div class="text-h4 q-mb-md">Daily Sales Report</div>

    <div class="row q-col-gutter-md q-mb-md items-center">
      <div class="col-auto">
        <q-input
          filled
          v-model="selectedDate"
          mask="##-##-####"
          label="Select Date"
          hint="MM-DD-YYYY"
          :rules="['##-##-####']"
        >
          <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                <q-date v-model="selectedDate" mask="MM-DD-YYYY" />
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
      <div class="col-auto">
        <q-btn
          label="Generate Report"
          color="primary"
          @click="fetchReport"
          :loading="salesStore.isLoading"
        />
      </div>
      <div class="col-grow text-right">
        <div v-if="!salesStore.isLoading && !salesStore.error" class="text-h5 text-weight-bold">
          Total Sales for {{ selectedDate }}: ${{ salesStore.totalDailySales.toFixed(2) }}
        </div>
      </div>
    </div>

    <div v-if="salesStore.isLoading" class="q-pa-md text-center">
      <q-spinner-dots color="primary" size="3em" />
      <div class="q-mt-md">Loading daily sales...</div>
    </div>

    <div v-else-if="salesStore.error" class="q-pa-md text-negative text-center">
      <q-icon name="error" size="2em" class="q-mb-sm" />
      <div>Error: {{ salesStore.error }}</div>
    </div>

    <q-table
      v-else
      title="Orders for the Day"
      :rows="salesStore.dailySales"
      :columns="columns"
      row-key="order_id"
      flat
      bordered
      separator="cell"
      class="q-mt-md"
      v-model:pagination="initialPagination"
      :filter="filter"
      :rows-per-page-options="[5, 10, 20, 50, 0]"
    >
      <template v-slot:top-right>
        <q-input outlined dense debounce="300" v-model="filter" placeholder="Search orders...">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>
    </q-table>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useSalesStore } from 'src/stores/sales-store'
import { format } from 'date-fns'

export default defineComponent({
  name: 'DailySalesReportPage',
  setup() {
    const salesStore = useSalesStore()
    const router = useRouter()
    const filter = ref('')

    const selectedDate = ref(format(new Date(), 'MM-dd-yyyy'))

    const initialPagination = ref({
      sortBy: 'order_date',
      descending: false,
      page: 1,
      rowsPerPage: 10,
    })

    const columns = [
      { name: 'order_id', align: 'left', label: 'Order ID', field: 'order_id', sortable: true },
      {
        name: 'order_date',
        align: 'left',
        label: 'Order Date & Time',
        field: 'order_date',
        format: (val) => format(new Date(val), 'MM/dd/yyyy HH:mm'),
        sortable: true,
      },
      {
        name: 'order_total_sales',
        align: 'right',
        label: 'Order Total',
        field: 'order_total_sales',
        format: (val) => `$${val.toFixed(2)}`,
        sortable: true,
      },
    ]

    const fetchReport = () => {
      if (selectedDate.value) {
        salesStore.fetchDailyOrdersWithDetails(selectedDate.value)
      }
    }

    const goToLogin = () => {
      router.push({ name: 'Login' })
    }

    onMounted(() => {
      fetchReport()
    })

    return {
      salesStore,
      selectedDate,
      filter,
      columns,
      initialPagination,
      fetchReport,
      goToLogin,
    }
  },
})
</script>