import { test, expect } from "@playwright/test";

// Authentication before test
test.beforeEach(async ({ page }) => {
  await page.goto("http://localhost:5173");
  await page.getByRole("textbox", { name: "Email" }).fill("user3@example.com");
  await page.getByRole("textbox", { name: "Password" }).fill("password3");
  await page.getByRole("button", { name: "Se connecter" }).click();
  await expect(page.getByRole("region", { name: "Map" })).toBeVisible();
});

test.use({
  permissions: ["geolocation"],
  geolocation: { longitude: 2.2002996219647377, latitude: 48.88957608308815 },
});

test.describe("Spot creation", () => {
  test("should allow user to create a new spot", async ({ page }) => {
    await page.getByTestId("add-spot").click();
    await page.getByRole("button", { name: "Sur ma position" }).click();
    await page.getByRole("button", { name: "C'est ici !" }).click();
    await page.getByRole("textbox", { name: "Description" }).click();
    await page
      .getByRole("textbox", { name: "Description" })
      .fill("Test E2E création spot");
    await page.getByRole("button", { name: "Enregistrer" }).click();

    await expect(
      page
        .locator("div")
        .filter({ hasText: "Spot ajouté à la map avec" })
        .nth(4)
    ).toBeVisible();

    await page.getByRole("img", { name: "Map marker" }).nth(-1).click();    
    await expect(page.getByText("Test E2E création spot")).toBeVisible();
  });
});
