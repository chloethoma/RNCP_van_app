import { Link } from "react-router";
import Logo from "../../assets/logo_transparent.svg";

interface FormHeaderProps {
  title: string,
  text: string,
  pathRedirection: string,
  textLink: string
}

function FormHeader({ title, text, pathRedirection, textLink }: FormHeaderProps) {
  return (
    <>
      <div className="flex flex-col items-center mb-2">
        <img src={Logo} alt="Logo" className="w-9/12"/>
      </div>
      <div className="flex flex-col items-center mb-2">
        <h2 className="text-2xl font-default font-semibold text-dark-grey text-center sm:text-2xl">
          {title}
        </h2>
        <p className="text-xs text-dark-grey mt-1 px-4">
          {text}{" "}
          <Link
            to={pathRedirection}
            className="mt-4 text-xs text-center text-light"
          >
            {textLink}
          </Link>
        </p>
      </div>
    </>
  );
}

export default FormHeader;
