import Header from "./Header";
import { ReactNode } from "react";

interface PageWithHeaderProps {
  text: string;
  children: ReactNode;
}

function ViewWithHeader({ text, children }: PageWithHeaderProps) {
  return (
    <>
      <Header text={text} />
      <div className="pt-14">{children}</div>
    </>
  );
}

export default ViewWithHeader;
