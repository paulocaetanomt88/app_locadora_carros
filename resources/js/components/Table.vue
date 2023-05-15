<template>
  <div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col" v-for="t, key in titulos" :key="key" class="text-uppercase">{{t}}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="obj in dados" :key="obj.id">
            <td v-if="titulos.includes(chave)" v-for="valor, chave in obj" :key="chave">
                <span v-if="chave == 'imagem'">
                    <img :src="'/storage/'+valor" width="30" height="30">
                </span>
                <span v-else>
                    {{valor}}
                </span>
            </td>
          <!--  
          <th scope="row">{{ marca.id }}</th>
          <td>{{ marca.nome }}</td>
          <td><img :src="'/storage/'+marca.imagem" width="30" height="30"></td>
          -->
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: ["dados", 'titulos'],
};
</script>