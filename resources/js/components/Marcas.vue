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
                  />
                </input-container-component>
              </div>
            </div>
          </template>
          <template v-slot:rodape>
            <button type="submit" class="btn btn-primary btn-sm float-right">
              Pesquisar
            </button>
          </template>
        </card-component>
        <!-- Fim do card de busca de marcas -->

        <!-- Início do card de listagem de marcas -->
        <card-component titulo="Listagem de Marcas">
          <template v-slot:conteudo>
            <table-component></table-component>
          </template>
          <template v-slot:rodape>
            <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">
              Adicionar
            </button>
          </template>
        </card-component>
        <!-- Fim do card de listagem de marcas -->
      </div>
    </div>
    <modal-component id="modalMarca" titulo="Adicionar marca"></modal-component>
  </div>
</template>

<script>
export default {};
</script>
