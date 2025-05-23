<template>
  <q-page padding>
    <div class="text-h4 q-mb-md">Orders</div>

    <div v-if="orderStore.isLoading" class="q-pa-md text-center">
      <q-spinner-dots color="primary" size="3em" />
      <div class="q-mt-md">Loading orders...</div>
    </div>

    <div v-else-if="orderStore.error" class="q-pa-md text-negative text-center">
      <q-icon name="error" size="2em" class="q-mb-sm" />
      <div>Error: {{ orderStore.error }}</div>
    </div>

    <q-table
      v-else
      title="Orders"
      :rows="orderStore.orders"
      :columns="columns"
      row-key="order_id"
      flat
      bordered
      separator="cell"
      class="q-mt-md"
      v-model:pagination="pagination"
      @request="onRequest"
      :loading="orderStore.isLoading"
      :filter="filter"
      :rows-per-page-options="pagination.rowsPerPageOptions"
    >
      <template v-slot:top-right>
        <q-input outlined dense debounce="1000" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

      <template v-slot:body-cell-actions="props">
        <q-td :props="props">
          <q-btn
            icon="visibility"
            label="Details"
            color="primary"
            flat
            dense
            @click="showOrderDetails(props.row)"
          />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="showDetailsModal">
      <q-card style="width: 700px; max-width: 80vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Order #{{ selectedOrder?.order_id }} Details</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section v-if="selectedOrder">
          <div class="text-subtitle1 q-mb-sm">
            Order Date: {{ formatOrderDate(selectedOrder.order_date) }}
          </div>

          <q-list bordered separator>
            <q-item-label header>Pizzas in this Order:</q-item-label>
            <q-item v-for="detail in selectedOrder.order_details" :key="detail.order_details_id">
              <q-item-section>
                <q-item-label>
                  {{ detail.quantity }}x {{ detail.pizza?.pizza_type?.name || 'Unknown Pizza' }} ({{
                    detail.pizza?.size || 'N/A'
                  }})
                </q-item-label>
                <q-item-label caption>
                  Price per item: ${{ detail.pizza?.price || '0.00' }}
                </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label
                  >${{ (detail.quantity * (detail.pizza?.price || 0)).toFixed(2) }}</q-item-label
                >
              </q-item-section>
            </q-item>
            <q-separator />
            <q-item>
              <q-item-section class="text-weight-bold">Total Order Value:</q-item-section>
              <q-item-section side class="text-weight-bold">
                ${{ calculateTotalOrderValue(selectedOrder.order_details) }}
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>

        <q-card-section v-else>
          <div class="text-center">No order selected or details available.</div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useOrderStore } from 'src/stores/order-store'
import { format } from 'date-fns'

export default defineComponent({
  name: 'OrderPage',
  setup() {
    const orderStore = useOrderStore()
    const router = useRouter()
    const filter = ref('')

    // State for the modal
    const showDetailsModal = ref(false)
    const selectedOrder = ref(null)

    const pagination = ref({
      sortBy: 'order_id',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
      rowsPerPageOptions: [5, 10, 20, 50],
    })

    const columns = [
      { name: 'order_id', align: 'left', label: 'Order ID', field: 'order_id', sortable: true },
      {
        name: 'order_date',
        align: 'left',
        label: 'Order Date & Time',
        field: 'order_date',
        format: (val) => formatOrderDate(val),
        sortable: true,
      },
      { name: 'actions', align: 'center', label: 'Actions', field: 'actions' },
    ]

    // Function to format the order_date for display
    const formatOrderDate = (dateString) => {
      if (!dateString) return 'N/A'
      return format(new Date(dateString), 'MMM dd, yyyy HH:mm:ss')
    }

    const calculateTotalOrderValue = (orderDetails) => {
      if (!orderDetails || orderDetails.length === 0) return '0.00'
      const total = orderDetails.reduce((sum, detail) => {
        const price = parseFloat(detail.pizza?.price || 0)
        return sum + detail.quantity * price
      }, 0)
      return total.toFixed(2)
    }

    const showOrderDetails = (order) => {
      selectedOrder.value = order
      showDetailsModal.value = true
    }

    const onRequest = async (props) => {
      const { page, rowsPerPage, sortBy, descending } = props.pagination
      const filterValue = props.filter

      pagination.value.sortBy = sortBy
      pagination.value.descending = descending
      pagination.value.page = page
      pagination.value.rowsPerPage = rowsPerPage

      await orderStore.fetchOrders({
        page: page,
        per_page: rowsPerPage,
        sort_by: sortBy,
        descending: descending,
        filter: filterValue,
      })

      pagination.value.rowsNumber = orderStore.totalOrders
    }

    watch(filter, (newFilter) => {
      pagination.value.page = 1
      onRequest({
        pagination: pagination.value,
        filter: newFilter,
      })
    })

    const goToLogin = () => {
      router.push({ name: 'Login' })
    }

    onMounted(() => {
      onRequest({
        pagination: pagination.value,
        filter: filter.value,
      })
    })

    return {
      orderStore,
      columns,
      filter,
      pagination,
      onRequest,
      goToLogin,
      showDetailsModal,
      selectedOrder,
      showOrderDetails,
      formatOrderDate,
      calculateTotalOrderValue,
    }
  },
})
</script>

<style lang="scss" scoped></style>
