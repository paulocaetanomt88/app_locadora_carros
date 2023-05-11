<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <!-- Início do card de busca  -->
        <div class="card mb-3">
          <div class="card-header">Busca de Marcas</div>

          <div class="card-body">
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
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-sm float-right">
              Pesquisar
            </button>
          </div>
        </div>
        <!-- Fim do card de busca de marcas -->

        <!-- Início do card de listagem de marcas -->
        <div class="card">
          <div class="card-header">Listagem de Marcas</div>

          <div class="card-body">
            <table-component></table-component>
          </div>

          <div class="card-footer">
            <button type="button" class="btn btn-primary btn-sm float-right">
              Adicionar
            </button>
          </div>
        </div>
        <!-- Fim do card de listagem de marcas -->
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>
