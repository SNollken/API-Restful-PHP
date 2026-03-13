<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Estoque - API RESTful</title>
    <style>
      :root {
        color-scheme: light;
        --bg: #0f172a;
        --panel: #111827;
        --panel-2: #0b1220;
        --text: #e2e8f0;
        --muted: #94a3b8;
        --accent: #38bdf8;
        --danger: #f87171;
        --success: #34d399;
        --border: #1f2937;
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        font-family: "Segoe UI", sans-serif;
        background: radial-gradient(circle at top, #0b1220, #020617);
        color: var(--text);
        min-height: 100vh;
        padding: 32px;
      }

      .container {
        max-width: 1100px;
        margin: 0 auto;
      }

      header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
      }

      h1 {
        margin: 0;
        font-size: 24px;
        letter-spacing: 0.5px;
      }

      .status {
        font-size: 14px;
        color: var(--muted);
      }

      .panel {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        margin-bottom: 24px;
      }

      form {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
      }

      form input,
      form textarea {
        width: 100%;
        padding: 10px 12px;
        background: var(--panel-2);
        border: 1px solid var(--border);
        color: var(--text);
        border-radius: 8px;
        font-size: 14px;
      }

      form textarea {
        grid-column: span 4;
        min-height: 60px;
        resize: vertical;
      }

      .actions {
        grid-column: span 4;
        display: flex;
        gap: 12px;
      }

      button {
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
      }

      .btn-primary {
        background: var(--accent);
        color: #0b1220;
      }

      .btn-secondary {
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--border);
      }

      .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
      }

      .table th,
      .table td {
        padding: 12px;
        border-bottom: 1px solid var(--border);
        text-align: left;
      }

      .table th {
        color: var(--muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.08em;
      }

      .table tr:hover {
        background: rgba(56, 189, 248, 0.08);
      }

      .table-actions button {
        margin-right: 8px;
      }

      .btn-danger {
        background: var(--danger);
        color: #0b1220;
      }

      .btn-edit {
        background: var(--success);
        color: #0b1220;
      }

      .message {
        margin-top: 12px;
        font-size: 14px;
        color: var(--muted);
      }

      @media (max-width: 900px) {
        form {
          grid-template-columns: 1fr 1fr;
        }

        form textarea,
        .actions {
          grid-column: span 2;
        }
      }

      @media (max-width: 600px) {
        body {
          padding: 16px;
        }

        form {
          grid-template-columns: 1fr;
        }

        form textarea,
        .actions {
          grid-column: span 1;
        }

        .table {
          font-size: 13px;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <header>
        <div>
          <h1>Controle de Estoque</h1>
          <div class="status" id="status-text">Carregando produtos...</div>
        </div>
      </header>

      <section class="panel">
        <form id="product-form">
          <input type="hidden" id="product-id" />
          <input type="text" id="name" placeholder="Nome do produto" required />
          <input type="number" id="price" placeholder="Preço" step="0.01" min="0" required />
          <input type="number" id="stock" placeholder="Estoque" min="0" required />
          <textarea id="description" placeholder="Descrição (opcional)"></textarea>
          <div class="actions">
            <button class="btn-primary" type="submit" id="submit-btn">Salvar</button>
            <button class="btn-secondary" type="button" id="cancel-btn">Cancelar edição</button>
          </div>
        </form>
        <div class="message" id="message"></div>
      </section>

      <section class="panel">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Produto</th>
              <th>Preço</th>
              <th>Estoque</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="products-body"></tbody>
        </table>
      </section>
    </div>

    <script>
      const apiBase = "/api/v1/products";
      const statusText = document.getElementById("status-text");
      const productsBody = document.getElementById("products-body");
      const form = document.getElementById("product-form");
      const message = document.getElementById("message");
      const cancelBtn = document.getElementById("cancel-btn");
      const submitBtn = document.getElementById("submit-btn");

      const formFields = {
        id: document.getElementById("product-id"),
        name: document.getElementById("name"),
        price: document.getElementById("price"),
        stock: document.getElementById("stock"),
        description: document.getElementById("description"),
      };

      function setMessage(text, isError = false) {
        message.textContent = text;
        message.style.color = isError ? "#f87171" : "#94a3b8";
      }

      function resetForm() {
        form.reset();
        formFields.id.value = "";
        submitBtn.textContent = "Salvar";
      }

      function formatPrice(value) {
        return Number(value).toLocaleString("pt-BR", {
          style: "currency",
          currency: "BRL",
        });
      }

      async function fetchProducts() {
        statusText.textContent = "Carregando produtos...";
        try {
          const response = await fetch(apiBase, {
            headers: { Accept: "application/json" },
          });
          const data = await response.json();
          const products = data.data || [];
          renderProducts(products);
          statusText.textContent = `${products.length} produtos encontrados`;
        } catch (error) {
          statusText.textContent = "Erro ao carregar produtos";
          setMessage("Não foi possível carregar os produtos.", true);
        }
      }

      function renderProducts(products) {
        productsBody.innerHTML = "";
        if (products.length === 0) {
          productsBody.innerHTML = `
            <tr>
              <td colspan="5">Nenhum produto cadastrado.</td>
            </tr>
          `;
          return;
        }

        products.forEach((product) => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${product.id}</td>
            <td>${product.name}</td>
            <td>${formatPrice(product.price)}</td>
            <td>${product.stock}</td>
            <td class="table-actions">
              <button class="btn-edit" data-action="edit">Editar</button>
              <button class="btn-danger" data-action="delete">Excluir</button>
            </td>
          `;

          row.querySelector('[data-action="edit"]').addEventListener("click", () => {
            formFields.id.value = product.id;
            formFields.name.value = product.name;
            formFields.price.value = product.price;
            formFields.stock.value = product.stock;
            formFields.description.value = product.description || "";
            submitBtn.textContent = "Atualizar";
            setMessage(`Editando produto #${product.id}`);
          });

          row.querySelector('[data-action="delete"]').addEventListener("click", async () => {
            if (!confirm(`Excluir "${product.name}"?`)) {
              return;
            }
            try {
              const response = await fetch(`${apiBase}/${product.id}`, {
                method: "DELETE",
                headers: { Accept: "application/json" },
              });
              if (!response.ok) {
                throw new Error("Erro ao excluir");
              }
              setMessage("Produto excluído com sucesso.");
              await fetchProducts();
            } catch (error) {
              setMessage("Erro ao excluir produto.", true);
            }
          });

          productsBody.appendChild(row);
        });
      }

      form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const payload = {
          name: formFields.name.value.trim(),
          price: Number(formFields.price.value),
          stock: Number(formFields.stock.value),
          description: formFields.description.value.trim() || null,
        };

        const isEdit = Boolean(formFields.id.value);
        const url = isEdit ? `${apiBase}/${formFields.id.value}` : apiBase;
        const method = isEdit ? "PUT" : "POST";

        try {
          const response = await fetch(url, {
            method,
            headers: {
              "Content-Type": "application/json",
              Accept: "application/json",
            },
            body: JSON.stringify(payload),
          });

          if (!response.ok) {
            throw new Error("Erro ao salvar");
          }

          setMessage(isEdit ? "Produto atualizado!" : "Produto criado!");
          resetForm();
          await fetchProducts();
        } catch (error) {
          setMessage("Erro ao salvar produto. Verifique os campos.", true);
        }
      });

      cancelBtn.addEventListener("click", () => {
        resetForm();
        setMessage("Edição cancelada.");
      });

      fetchProducts();
    </script>
  </body>
</html>
