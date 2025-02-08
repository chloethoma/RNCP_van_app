import { useState } from "react";
import { useNavigate } from "react-router";
import { registerUser } from "../services/api/apiRequests";
import FormHeader from "../components/form/Header";
import FormButton from "../components/form/Button";
import FormInput from "../components/form/Input";
import Form from "../components/form/Form";

function Register() {
  const navigate = useNavigate();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmedPassword, setConfirmedPassword] = useState("");
  const [pseudo, setPseudo] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [errorMessage, setErrorMessage] = useState("");

  async function handleRegistration(e) {
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
      console.log(error);
      setErrorMessage(error.response.data.error.message);
      setTimeout(() => setErrorMessage(""), 3000);
    }
  }

  return (
    <div className="flex items-center justify-center min-h-screen bg-light-green">
      <div className="w-full max-w-sm p-8 pt-0 rounded-lg shadow-md sm:p-8">
        {errorMessage && (
          <div className="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg">
            {errorMessage}
          </div>
        )}
        <FormHeader
          title={"Inscription"}
          text={"Vous avez déjà un compte ?"}
          pathRedirection={"/login"}
          textLink={"Me connecter"}
        />
        <Form handleAction={handleRegistration}>
          <FormInput
            label={"Email"}
            type={"email"}
            placeHolder={"Entrez votre email"}
            value={email}
            onChange={setEmail}
          />
          <FormInput
            label={"Pseudo"}
            type={"text"}
            placeHolder={"Entrez votre pseudo"}
            value={pseudo}
            onChange={setPseudo}
          />
          <FormInput
            label={"Mot de passe"}
            type={"password"}
            placeHolder={"Entrez votre mot de passe"}
            value={password}
            onChange={setPassword}
          />
          <FormInput
            label={"Confirmez votre mot de passe"}
            type={"password"}
            placeHolder={"Confirmez votre mot de passe"}
            value={confirmedPassword}
            onChange={setConfirmedPassword}
          />
          {passwordError && (
            <p className="text-red-500 text-xs mt-1">{passwordError}</p>
          )}
          <FormButton value={"M'inscrire"} />
        </Form>
      </div>
    </div>
  );
}

export default Register;
