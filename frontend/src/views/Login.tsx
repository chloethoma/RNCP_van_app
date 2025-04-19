import { useId, useState } from "react";
import { Link, useNavigate } from "react-router";
import { loginUser } from "../services/api/apiRequests";
import FormButton from "../components/buttons/FormButton";
import Logo from "../assets/logo_transparent.svg";
import ErrorMessage from "../components/messages/ErrorMessage";
import { messages } from "../services/helpers/messagesHelper";

interface FormInputProps {
  label: string;
  type: "email" | "password" | "text";
  placeholder: string;
  value: string;
  onChange: (value: string) => void;
}

function Login() {
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
      setErrorMessage(error instanceof Error ? error.message : messages.error_default);
    }
  }

  return (
    <div className="flex items-center justify-center min-h-screen bg-light-green">
      <div className="w-full max-w-sm p-8 pt-0 rounded-lg sm:p-8">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />

        {/* Header */}
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

        {/* Form */}
        <form onSubmit={handleLogin}>
          <Input
            label={"Email"}
            type={"email"}
            placeholder={"Entrez votre email"}
            value={email}
            onChange={setEmail}
          />
          <Input
            label={"Password"}
            type={"password"}
            placeholder={"Entrez votre mot de passe"}
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
