document.addEventListener("DOMContentLoaded", () => {
  const formatPrice = (value) => {
    const number = Number.parseFloat(value || "0");
    return `RM ${number.toLocaleString("en-MY", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  };

  document.querySelectorAll("[data-color-options]").forEach((group) => {
    group.addEventListener("click", (event) => {
      const button = event.target.closest(".color-option");
      if (!button) return;

      group.querySelectorAll(".color-option").forEach((item) => item.classList.remove("selected"));
      button.classList.add("selected");

      const selectedInput = document.querySelector("[data-selected-color]");
      if (selectedInput) {
        selectedInput.value = button.dataset.colorName || "";
      }

      const image = document.querySelector(".detail-media .real-product-img");
      if (image && button.dataset.colorImage) {
        image.src = button.dataset.colorImage;
        image.style.display = "";
      }
    });
  });

  document.querySelectorAll("[data-variant-options]").forEach((group) => {
    group.addEventListener("click", (event) => {
      const button = event.target.closest(".variant-option");
      if (!button) return;

      group.querySelectorAll(".variant-option").forEach((item) => item.classList.remove("selected"));
      button.classList.add("selected");

      const price = button.dataset.variantPrice || "0";
      const priceDisplay = document.querySelector("[data-product-price]");
      const selectedVariant = document.querySelector("[data-selected-variant]");
      const selectedPrice = document.querySelector("[data-selected-price]");

      if (priceDisplay) priceDisplay.textContent = formatPrice(price);
      if (selectedVariant) selectedVariant.value = button.dataset.variantName || "";
      if (selectedPrice) selectedPrice.value = price;
    });
  });

  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", () => {
      const button = form.querySelector("button[type='submit']");
      if (button) {
        button.dataset.originalText = button.textContent;
      }
    });
  });

  document.querySelectorAll("[data-add-color-row]").forEach((button) => {
    button.addEventListener("click", () => {
      const editor = button.previousElementSibling;
      const lastRow = editor?.querySelector(".color-image-row:last-child");
      if (!lastRow) return;

      const newRow = lastRow.cloneNode(true);
      newRow.querySelectorAll("input").forEach((input) => {
        if (input.type === "color") {
          input.value = "#d8d8d8";
        } else {
          input.value = "";
        }
      });
      newRow.querySelector(".color-image-current")?.replaceChildren();
      editor.appendChild(newRow);
    });
  });
});
