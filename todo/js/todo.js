const todoApp = new Vue({
    el: '#apptodo',
    data: {
      newItem: {
        text: null,
        due: null,
        completed: false
      },
      todos: new Array(),
      csrfToken: null
    },

  methods: {
    addNew() {
      if (!this.todos.length) {
        this.todos = new Array();
      }
      this.todos.push(this.newItem);
      this.newItem = {
        text: null,
        due: null,
        completed: false
      };
    },

    update(data){
      if(null === this.csrfToken) {
        return;
      }
      console.log('state data',this.csrfToken, data);

      fetch(`/api/vue/todo?_format=json`, {
        method: 'POST',
        mode: "cors",
        cache: "no-cache",
        credentials: "same-origin",
        headers: {
          'X-CSRF-Token': this.csrfToken,
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Methods': 'POST',
          'Access-Control-Allow-Headers': 'Content-Type'
        },
        body: JSON.stringify(data)
      });
    },
  },

  watch: {
    todos(newTodos){
      console.log('watch',newTodos);
      todoApp.update(newTodos);
    }
  },

  mounted() {

  axios.get('/api/vue/todo?_format=json').then(
    response => (this.todos = response.data)
  );


  axios.get('/session/token').then(
    response => (this.csrfToken = response.data)
  );
}
});
