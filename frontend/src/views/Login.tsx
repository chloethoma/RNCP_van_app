import { useState } from "react";
import { useNavigate} from "react-router";
import { loginUser } from "../services/api/apiRequests";
import FormHeader from "../components/form/Header";
import FormButton from "../components/form/Button";
import FormInput from "../components/form/Input";
import Form from "../components/form/Form";
import { AxiosError } from "axios";

function Login() {
  const navigate = useNavigate();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errorMessage, setErrorMessage] = useState("");

  async function handleLogin(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    try {
      const requestBody = { email, password };
      await loginUser(requestBody);
      navigate("/");
    } catch (error) {
      if (error instanceof AxiosError) {
        setErrorMessage(error.response?.data?.error?.message || "Échec de l'authentification.");
      } else {
        setErrorMessage("Une erreur inconnue est survenue.");
        console.error("Erreur inconnue :", error);
      }
      setTimeout(() => setErrorMessage(""), 3000);
    }
  }

  return (
    <div className="flex items-center justify-center min-h-screen bg-light-green">
      <div className="w-full max-w-sm p-8 pt-0 rounded-lg sm:p-8">
        {errorMessage && (
          <div className="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg">
            {errorMessage}
          </div>
        )}

        <FormHeader
          title={"Connexion"}
          text={"Vous n'avez pas de compte ?"}
          pathRedirection={"/register"}
          textLink={"Créer un compte"}
        />
        <Form handleAction={handleLogin}>
          <FormInput
            label={"Email"}
            type={"email"}
            placeHolder={"Entrez votre email"}
            value={email}
            onChange={setEmail}
          />
          <FormInput
            label={"Password"}
            type={"password"}
            placeHolder={"Entrez votre mot de passe"}
            value={password}
            onChange={setPassword}
          />
          <FormButton>Se connecter</FormButton>
        </Form>
      </div>
    </div>
  );
}

export default Login;
