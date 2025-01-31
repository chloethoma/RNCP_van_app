import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router";
import "./index.css";
import React from "react";
import Routing from "./services/routes/Routing";

createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <BrowserRouter>
      <Routing />
    </BrowserRouter>
  </React.StrictMode>
);
