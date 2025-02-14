import { useId, useState } from "react";
import { Link, useNavigate } from "react-router";
import { loginUser } from "../services/api/apiRequests";
import FormButton from "../components/buttons/FormButton";
import { AxiosError } from "axios";
import Logo from "../assets/logo_transparent.svg";

interface FormInputProps {
  label: string;
  type: "email" | "password" | "text";
  placeHolder: string;
  value: string;
  onChange: (value: string) => void;
}

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
        setErrorMessage(
          error.response?.data?.error?.message || "Échec de l'authentification."
        );
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

        <div className="flex flex-col items-center mb-2">
          <img src={Logo} alt="Logo" className="w-9/12" />
        </div>
        <div className="flex flex-col items-center mb-2">
          <h2 className="text-2xl font-default font-semibold text-dark-grey text-center sm:text-2xl">
            Connexion
          </h2>
          <p className="text-xs text-dark-grey mt-1 px-4">
            Vous n'avez pas de compte ?{" "}
            <Link
              to={"/register"}
              className="mt-4 text-xs text-center text-light"
            >
              Créer un compte
            </Link>
          </p>
        </div>

        <form onSubmit={handleLogin}>
          <Input
            label={"Email"}
            type={"email"}
            placeHolder={"Entrez votre email"}
            value={email}
            onChange={setEmail}
          />
          <Input
            label={"Password"}
            type={"password"}
            placeHolder={"Entrez votre mot de passe"}
            value={password}
            onChange={setPassword}
          />
          <FormButton>Se connecter</FormButton>
        </form>
      </div>
    </div>
  );
}

function Input({ label, onChange, ...inputProps }: FormInputProps) {
  const id = useId();
  return (
    <div className="mt-3">
      <label htmlFor={id} className="block text-sm font-medium text-dark-grey">
        {label}
      </label>

      <input
        id={id}
        className="mt-1 block w-full px-3 py-2 bg-light-grey border border-light-grey rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-dark-green focus:border-transparent sm:text-sm"
        {...inputProps}
        onChange={(e) => onChange(e.target.value)}
      />
    </div>
  );
}


export default Login;
