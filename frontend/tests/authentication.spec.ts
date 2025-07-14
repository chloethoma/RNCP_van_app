import { test, expect } from "@playwright/test";

test.beforeEach(async ({ page }) => {
  await page.goto("http://localhost:5173");
});

test.describe("Authentication", () => {
    test("should display all required elements on the login page", async ({ page }) => {
    await expect(page.getByRole("img", { name: "Logo" })).toBeVisible();
    await expect(page.getByRole("heading", { name: "Connexion" })).toBeVisible();
    await expect(page.getByText("Email")).toBeVisible();
    await expect(page.getByRole("textbox", { name: "Email" })).toBeVisible();
    await expect(page.getByText("Password")).toBeVisible();
    await expect(page.getByRole("textbox", { name: "Password" })).toBeVisible();
    await expect(page.getByRole("button", { name: "Se connecter" })).toBeVisible();
  });

  test("should display an error message with invalid credentials", async ({ page }) => {
    await page.getByRole("textbox", { name: "Email" }).fill("wrongemail@example.com");
    await page.getByRole("textbox", { name: "Password" }).fill("password1");
    await page.getByRole("button", { name: "Se connecter" }).click();
    await expect(page.locator('div').filter({ hasText: /^Les identifiants ne sont pas corrects, veuillez réessayer\.$/ })).toBeVisible();  });

  test("should successfully log in with valid credentials", async ({page}) => {
    await page.getByRole("textbox", { name: "Email" }).fill("user3@example.com");
    await page.getByRole("textbox", { name: "Password" }).fill("password3");
    await page.getByRole("button", { name: "Se connecter" }).click();

    // Check redirection
    await expect(page.getByRole("region", { name: "Map" })).toBeVisible();
    await expect(page.locator('div.mapboxgl-marker').first()).toBeVisible(); 
    await expect(page.getByRole('link', { name: 'Home', exact: true })).toBeVisible();
    await expect(page.getByRole('button').nth(4)).toBeVisible();  
})
});
