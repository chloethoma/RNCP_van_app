import { useState } from "react";
import { Link, useNavigate } from "react-router";
import { loginUser } from "../services/api/apiRequests";
import FormSubmitButton from "../components/buttons/FormSubmitButton";
import Logo from "../assets/logo_transparent.svg";
import ErrorMessage from "../components/messages/ErrorMessage";
import { messages } from "../services/helpers/messagesHelper";
import FormInput from "../components/form/FormInput";

function Login() {
  const API_URL = import.meta.env.VITE_API_URL;

  const navigate = useNavigate();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  async function handleLogin(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    try {
      const requestBody = { email, password };
      await loginUser(requestBody);
      navigate("/");
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  }

  return (
    <div className="relative flex items-center justify-center h-screen bg-light-green">
      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />

      <div className="relative w-full max-w-sm p-8 pt-0 rounded-2xl border-2 border-border-grey shadow-lg">
        {/* Header */}
        <div className="flex flex-col items-center">
          <img src={Logo} alt="Logo" className="w-8/12" />
        </div>
        <div className="flex flex-col items-center mb-2">
          <h2 className="text-2xl font-default font-semibold text-dark-grey text-center">
            Connexion
          </h2>
          <p className="text-xs text-dark-grey mt-1 px-4">
            Vous n'avez pas de compte ?{" "}
            <Link
              to={"/register"}
              className="text-xs text-center text-light/90 font-medium hover:underline hover:text-light transition-colors duration-200"
            >
              Créer un compte
            </Link>
          </p>
        </div>

        {/* Form */}
        <form onSubmit={handleLogin}>
          <FormInput
            label={"Email"}
            type={"email"}
            placeholder={"Entrez votre email"}
            value={email}
            onChange={setEmail}
            required
          />
          <FormInput
            label={"Password"}
            type={"password"}
            placeholder={"Entrez votre mot de passe"}
            value={password}
            onChange={setPassword}
            required
          />
          <FormSubmitButton>Se connecter</FormSubmitButton>
        </form>

        {/* Reset password */}
        <div className="flex flex-col items-center mb-2">
          <p className="text-xs text-dark-grey mt-1 px-4 pt-2">
            Mot de passe oublié ?{" "}
            <a
              href={`${API_URL}/reset-password`}
              target="_blank"
              rel="noopener noreferrer"
              className="text-xs text-center text-light/90 font-medium hover:underline hover:text-light transition-colors duration-200"
            >
              Cliquez ici
            </a>
          </p>
        </div>
      </div>
    </div>
  );
}

export default Login;
