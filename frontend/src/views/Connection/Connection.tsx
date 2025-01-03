
function Connection({onLogIn}) {
  const handleLogIn = () => {
    onLogIn(true);
  }

    return (
        <>
          <div>Connectez-vous pour voir vos spots !</div>
          <button onClick={handleLogIn}>Connexion</button>
        </>
      );
    
}

export default Connection;

