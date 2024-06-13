document.addEventListener('DOMContentLoaded', () => {
  const elements = document.querySelectorAll('.tom-select')
  elements.forEach(element => {
    new TomSelect(element, {
      persist: false,
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      load: async (query, callback) => {
        const response = await fetch(`/api/products?name=${encodeURIComponent(query)}`, {
          headers: { Accept: 'application/json' }
        })
        const { items } = await response.json()
        const options = []
        items.forEach(({ name, variants }) => {
          variants.forEach(({ id, choiceLabel }) => {
            options.push({ id, name: choiceLabel ? `${name}【${choiceLabel}】` : name })
          })
        });
  
        callback(options)
      },
      render: {
        option: (item, escape) => `<div>${escape(item.name)}</div>`,
        item: (item, escape) => `<div>${escape(item.name)}</div>`,
      }
    })
  })
})
