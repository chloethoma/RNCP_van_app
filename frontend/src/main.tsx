import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router";
import "./index.css";
import React from "react";
import Routing from "./services/routes/Routing";
import { UserProvider } from "./hooks/UserContext";

createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <UserProvider>
      <BrowserRouter>
        <Routing />
      </BrowserRouter>
    </UserProvider>
  </React.StrictMode>,
);
