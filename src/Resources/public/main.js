import TomSelect from 'tom-select';

document.addEventListener('DOMContentLoaded', () => {
  const elements = document.querySelectorAll('.tom-select')
  elements.forEach(element => {
    new TomSelect(element, {
      persist: false,
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      load: async (query, callback) => {
        const headers = { Accept: 'application/json' }
        const response = await fetch(`/api/products?name=${encodeURIComponent(query)}`, { headers })
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
        option: (item, escape) => `<div class="asdf111">${escape(item.name)}</div>`,
        item: (item, escape) => `<div class="asdf222">${escape(item.name)}</div>`,
      }
    })
  })
})
