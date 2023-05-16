<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <!-- Início do card de busca  -->
        <card-component titulo="Busca de Marcas">
          <template v-slot:conteudo>
            <div class="form-row">
              <div class="col mb-3">
                <input-container-component
                  titulo="ID"
                  id="inputId"
                  id-help="idHelp"
                  texto-ajuda="Informe o ID da marca (opcional)"
                >
                  <input
                    type="number"
                    class="form-control"
                    id="inputId"
                    aria-describedby="idHelp"
                    placeholder="ID"
                    v-model = "busca.id"
                  />
                </input-container-component>
              </div>

              <div class="col mb-3">
                <input-container-component
                  titulo="Nome da marca"
                  id="inputNome"
                  id-help="nomeHelp"
                  texto-ajuda="Informe o nome da marca (opcional)"
                >
                  <input
                    type="text"
                    class="form-control"
                    id="inputNome"
                    aria-describedby="nomeHelp"
                    placeholder="Nome da marca"
                    v-model = "busca.nome"
                  />
                </input-container-component>
              </div>
            </div>
          </template>
          <template v-slot:rodape>
            <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">
              Pesquisar
            </button>
          </template>
        </card-component>
        <!-- Fim do card de busca de marcas -->

        <!-- Início do card de listagem de marcas -->
        <card-component titulo="Listagem de Marcas">
          <template v-slot:conteudo>
            <table-component
                :dados="marcas.data"
                :titulos=" {
                    id: {titulo: 'ID', tipo: 'texto'},
                    nome: {titulo: 'Nome', tipo: 'texto'},
                    imagem: {titulo: 'Imagem', tipo: 'imagem'},
                    created_at: {titulo: 'Cadastrada em', tipo: 'data'},
                }"
            ></table-component>
          </template>
          <template v-slot:rodape>
            <div class="row">
                <div class="col-10">
                    <paginate-component>
                        <li v-for="l, key in marcas.links" :key="key" :class="l.active ? 'page-item active':'page-item'" @click="paginacao(l)" >
                            <a class="page-link" v-html="l.label"></a>
                        </li>
                    </paginate-component>
                </div>
                <div class="col">
                    <button
                    type="button"
                    class="btn btn-primary btn-sm float-right"
                    data-toggle="modal"
                    data-target="#modalMarca"
                    >
                        Adicionar
                    </button>
                </div>
            </div>


          </template>
        </card-component>
        <!-- Fim do card de listagem de marcas -->
      </div>
    </div>
    <modal-component id="modalMarca" titulo="Adicionar marca">

      <template v-slot:alertas>
        <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="A marca foi cadastrada com sucesso." v-if="transacaoStatus == 'adicionado'"></alert-component>
        <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca" v-if="transacaoStatus == 'erro'"></alert-component>
      </template>

      <template v-slot:conteudo>
      <div class="form-group">
        <input-container-component
          titulo="Nome da marca"
          id="novoNome"
          id-help="novoNomeHelp"
          texto-ajuda="Informe o nome da marca"
        >
          <input
            type="text"
            class="form-control"
            id="novoNome"
            aria-describedby="novoNomeHelp"
            placeholder="Nome da marca"
            v-model="nomeMarca"
          />
        </input-container-component>
      </div>
      <div class="form-group">
        <input-container-component
          titulo="Imagem"
          id="novoImagem"
          id-help="novoImagemHelp"
          texto-ajuda="Selecione uma imagem no formato PNG"
        >
          <input
            type="file"
            class="form-control-file"
            id="novoImagem"
            aria-describedby="novoImagemHelp"
            placeholder="Selecione uma imagem"
            @change="carregarImagem($event)"
          />
        </input-container-component>
      </div>
      </template>
      <template v-slot:rodape>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Fechar
          </button>
        <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
      </template>
    </modal-component>
  </div>
</template>

<script>
export default {
  computed: {
    token() {
      let token = document.cookie.split(";").find((indice) => {
        return indice.includes("token=");
      });

      token = token.split("=")[1];
      token = "Bearer " + token;

      return token;
    },
  },
  data() {
    return {
      urlBase: "http://localhost:8000/api/v1/marca",
      urlPaginacao: '',
      urlFiltro: '',
      config: {
        headers: {
          "Content-Type": "multipart/form-data",
          Accept: "application/json",
          Authorization: this.token,
        },
      },
      nomeMarca: "",
      arquivoImagem: [],
      transacaoStatus: "",
      transacaoDetalhes: {},
      marcas: { data: [] },
      busca: { id: '', nome: '' }
    };
  },
  methods: {
    pesquisar() {
        console.log(this.busca)
        let filtro = ''

        for (let chave in this.busca) {
            if(this.busca[chave]) {
                if (filtro != '') {
                    filtro += ';'
                }

                filtro += chave + ':like:' + this.busca[chave]
            }
        }
        
        if (filtro != '') {
            this.urlPaginacao = 'page=1'
            this.urlFiltro = '&filtro='+filtro
        } else {
            this.urlFiltro = ''
        }

        this.carregarLista()
    },
    paginacao(l) {
        if (l.url) {
            // this.urlBase = l.url // ajustando a url com o parametro de página
            this.urlPaginacao = l.url.split('?')[1]
            this.carregarLista() // requisitando novamente os dados para nossa API
        }
    },
    carregarLista() {
      let config = {
        headers: {
          "Content-Type": "multipart/form-data",
          Accept: "application/json",
          Authorization: this.token,
        },
      };

      let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
      console.log(url)

      axios
        .get(url, config)
        .then((response) => {
          this.marcas = response.data;
        })
        .catch((errors) => {
          console.log(errors);
        });
    },
    carregarImagem(e) {
      this.arquivoImagem = e.target.files;
    },
    salvar() {
      // instanciando um formulário para que seja possível definir os seus atributos
      let formData = new FormData();
      formData.append("nome", this.nomeMarca);
      formData.append("imagem", this.arquivoImagem[0]);

      let config = {
        headers: {
          "Content-Type": "multipart/form-data",
          Accept: "application/json",
          Authorization: this.token,
        },
      };

      // o método post do axios espera por três parâmetros: url, conteúdo e a configuração contendo os headers
      axios
        .post(this.urlBase, formData, config)
        .then((response) => {
          this.transacaoStatus = "adicionado";

          this.transacaoDetalhes = {
            mensagem: "ID da marca: " + response.data.id,
          };

          console.log(response);
        })
        .catch((errors) => {
          this.transacaoStatus = "erro";
          this.transacaoDetalhes = {
            mensagem: errors.response.data.message,
            dados: errors.response.data.errors,
          };
          //  console.log(errors.response.data.message)
        });
    },
  },
  mounted() {
    this.carregarLista();
  },
};
</script>
