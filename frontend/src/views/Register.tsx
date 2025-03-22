import { useId, useState } from "react";
import { Link, useNavigate } from "react-router";
import { registerUser } from "../services/api/apiRequests";
import FormButton from "../components/buttons/FormButton";
import Logo from "../assets/logo_transparent.svg";
import ErrorMessage from "../components/messages/ErrorMessage";

interface FormInputProps {
  label: string;
  type: "email" | "password" | "text";
  placeholder: string;
  value: string;
  onChange: (value: string) => void;
}

function Register() {
  const navigate = useNavigate();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmedPassword, setConfirmedPassword] = useState("");
  const [pseudo, setPseudo] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  async function handleRegistration(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();

    if (password !== confirmedPassword) {
      setPasswordError("Les mots de passe ne sont pas identiques.");
      return;
    } else {
      setPasswordError("");
    }

    try {
      const requestBody = { email, password, pseudo };
      await registerUser(requestBody);
      navigate("/");
    } catch (error) {
      if (error instanceof Error) {
        setErrorMessage(error.message);
      } else {
        setErrorMessage("Erreur lors de l'inscription");
      }
    }
  }

  return (
    <div className="flex items-center justify-center min-h-screen bg-light-green">
      <div className="w-full max-w-sm p-8 pt-0 rounded-lg shadow-md sm:p-8">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />

        <div className="flex flex-col items-center mb-2">
          <img src={Logo} alt="Logo" className="w-9/12" />
        </div>
        <div className="flex flex-col items-center mb-2">
          <h2 className="text-2xl font-default font-semibold text-dark-grey text-center sm:text-2xl">
            Inscription
          </h2>
          <p className="text-xs text-dark-grey mt-1 px-4">
            Vous avez déjà un compte ?{" "}
            <Link to={"/login"} className="mt-4 text-xs text-center text-light">
              Me connecter
            </Link>
          </p>
        </div>

        <form onSubmit={handleRegistration}>
          <Input
            label={"Email"}
            type={"email"}
            placeholder={"Entrez votre email"}
            value={email}
            onChange={setEmail}
          />
          <Input
            label={"Pseudo"}
            type={"text"}
            placeholder={"Entrez votre pseudo"}
            value={pseudo}
            onChange={setPseudo}
          />
          <Input
            label={"Mot de passe"}
            type={"password"}
            placeholder={"Entrez votre mot de passe"}
            value={password}
            onChange={setPassword}
          />
          <Input
            label={"Confirmez votre mot de passe"}
            type={"password"}
            placeholder={"Confirmez votre mot de passe"}
            value={confirmedPassword}
            onChange={setConfirmedPassword}
          />
          {passwordError && (
            <p className="text-red-500 text-xs mt-1">{passwordError}</p>
          )}
          <FormButton>M'inscrire</FormButton>
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

export default Register;
