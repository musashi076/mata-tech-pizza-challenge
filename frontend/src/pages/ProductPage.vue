<template>
  <q-page padding>
    <div class="text-h4 q-mb-md">Products</div>

    <div v-if="productStore.isLoading" class="q-pa-md text-center">
      <q-spinner-dots color="primary" size="3em" />
      <div class="q-mt-md">Loading products...</div>
    </div>

    <div v-else-if="productStore.error" class="q-pa-md text-negative text-center">
      <q-icon name="error" size="2em" class="q-mb-sm" />
      <div>Error: {{ productStore.error }}</div>
    </div>

    <q-table
      v-else
      title="Pizzas"
      :rows="productStore.products"
      :columns="columns"
      row-key="pizza_id"
      flat
      bordered
      separator="cell"
      class="q-mt-md"
      v-model:pagination="pagination"
      @request="onRequest"
      :loading="productStore.isLoading"
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

      <template v-slot:body-cell-pizza_type_name="props">
        <q-td :props="props">
          {{ props.row.pizza_type ? props.row.pizza_type.name : 'N/A' }}
        </q-td>
      </template>

      <template v-slot:body-cell-ingredients="props">
        <q-td :props="props">
          <q-tooltip>
            {{ props.row.pizza_type ? props.row.pizza_type.ingredients : 'N/A' }}
          </q-tooltip>
          <span class="ellipsis-text">{{
            props.row.pizza_type ? props.row.pizza_type.ingredients : 'N/A'
          }}</span>
        </q-td>
      </template>
    </q-table>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from 'vue'
import { useProductStore } from 'src/stores/product-store'

export default defineComponent({
  name: 'ProductsPage',
  setup() {
    const productStore = useProductStore()
    const filter = ref('')

    const pagination = ref({
      sortBy: 'pizza_id',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
      rowsPerPageOptions: [5, 10, 20, 50],
    })

    const columns = [
      { name: 'pizza_id', align: 'left', label: 'Pizza ID', field: 'pizza_id', sortable: true },
      {
        name: 'pizza_type_name',
        align: 'left',
        label: 'Pizza Name',
        field: (row) => (row.pizza_type ? row.pizza_type.name : ''),
        sortable: true,
      },
      { name: 'size', align: 'left', label: 'Size', field: 'size', sortable: true },
      {
        name: 'price',
        align: 'right',
        label: 'Price',
        field: 'price',
        format: (val) => `$${val}`,
        sortable: true,
      },
      {
        name: 'category',
        align: 'left',
        label: 'Category',
        field: (row) => (row.pizza_type ? row.pizza_type.category : ''),
        sortable: true,
      },
      {
        name: 'ingredients',
        align: 'left',
        label: 'Ingredients',
        field: (row) => (row.pizza_type ? row.pizza_type.ingredients : ''),
        sortable: true,
        style: 'max-width: 200px',
        classes: 'ellipsis',
      },
    ]

    // q-table request event binding
    const onRequest = async (props) => {
      const { page, rowsPerPage, sortBy, descending } = props.pagination
      const filterValue = props.filter

      pagination.value.sortBy = sortBy
      pagination.value.descending = descending
      pagination.value.page = page
      pagination.value.rowsPerPage = rowsPerPage

      await productStore.fetchProducts({
        page: page,
        per_page: rowsPerPage,
        sort_by: sortBy,
        descending: descending,
        filter: filterValue,
      })

      pagination.value.rowsNumber = productStore.totalProducts
    }

    watch(filter, (newFilter) => {
      pagination.value.page = 1
      onRequest({
        pagination: pagination.value,
        filter: newFilter,
      })
    })

    onMounted(() => {
      onRequest({
        pagination: pagination.value,
        filter: filter.value,
      })
    })

    return {
      productStore,
      columns,
      filter,
      pagination,
      onRequest,
    }
  },
})
</script>

<style lang="scss" scoped>
.ellipsis-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 200px;
}
</style>
