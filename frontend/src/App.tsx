import { useState } from "react";
import "./App.css";
import UserMap from "./views/UserMap/UserMap";
import Connection from "./views/Connection/Connection";

function App() {
  const [isLoggedIn, setIsLoggedIn] = useState(true);

  return (
    <>
      {isLoggedIn ? <UserMap onLogOut={setIsLoggedIn} /> : <Connection onLogIn={setIsLoggedIn}/>}
    </>
  );
}

export default App;
