import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router";
import "./index.css";
import React from "react";
import Routes from "./services/routes/Routes.tsx";

createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <BrowserRouter>
      <Routes />
    </BrowserRouter>
  </React.StrictMode>
);
