<script>  
export default {   
  methods: { 
    mapResources(resources) {
      return resources.map(resource => {
        return _.tap({}, contact => {
          resource.fields.forEach(field => {
            contact[field.attribute] = field.value
          }) 
        }) 
      })
    }
  }, 
};
</script> 