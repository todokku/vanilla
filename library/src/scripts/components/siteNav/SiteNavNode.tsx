/*
 * @author Stéphane LaFlèche <stephane.l@vanillaforums.com>
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license GPL-2.0-only
 */

import * as React from "react";
import classNames from "classnames";
import { Link } from "react-router-dom";
import { downTriangle, rightTriangle } from "@library/components/Icons";
import Button, { ButtonBaseClass } from "@library/components/forms/Button";
import { t } from "@library/application";

interface IProps {
    name: string;
    className?: string;
    titleID?: string;
    children: any[];
    counter: number;
    url: string;
    openParent?: () => void;
    location: any;
    depth: number;
}

interface IState {
    open: boolean;
    current: boolean;
}

export default class SiteNavNode extends React.Component<IProps, IState> {
    public constructor(props) {
        super(props);
        this.state = {
            open: false,
            current: false,
        };
    }

    public open = () => {
        this.setState({
            open: true,
        });
    };

    public close = () => {
        this.setState({
            open: false,
        });
    };

    public openRecursive = () => {
        if (!this.state.current && this.currentPage()) {
            this.setState({
                current: true,
            });
            if (this.props.openParent) {
                this.props.openParent();
            }
        }
    };

    public openSelfAndOpenParent = () => {
        this.setState({
            open: true,
        });
        if (this.props.openParent) {
            this.open();
            this.props.openParent();
        }
    };

    public toggle = () => {
        this.setState({
            open: !this.state.open,
        });
    };

    public handleClick = e => {
        e.preventDefault();
        this.toggle();
    };

    public currentPage(): boolean {
        if (this.props.location && this.props.location.pathname) {
            return this.props.location.pathname === this.props.url;
        } else {
            return false;
        }
    }

    public updateCurrentState() {
        this.setState({
            current: this.currentPage(),
        });
    }

    public componentDidUpdate(prevProps) {
        this.openRecursive();
        if (prevProps.location.pathname !== this.props.location.pathname) {
            this.updateCurrentState();
        }
    }

    public componentDidMount() {
        this.openRecursive();
    }

    public render() {
        const hasChildren = this.props.children && this.props.children.length > 0;
        const topLevel = this.props.counter === 1;
        const childrenContents =
            hasChildren &&
            this.props.children.map((child, i) => {
                return (
                    <SiteNavNode
                        {...child}
                        key={"siteNavNode-" + this.props.counter + "-" + i}
                        counter={this.props.counter! + 1}
                        openParent={this.openSelfAndOpenParent}
                        location={this.props.location}
                        depth={this.props.depth + 1}
                    />
                );
            });
        const space = `&nbsp;`;
        return (
            <li
                role="treeitem"
                className={classNames("siteNavNode", this.props.className, { isCurrent: this.state.current })}
                aria-expanded={this.state.open}
                style={{ marginLeft: `${(this.props.depth - 1) * 18}px` }}
            >
                {hasChildren && (
                    <Button
                        tabIndex={-1}
                        ariaHidden={true}
                        title={t("Toggle Category")}
                        ariaLabel={t("Toggle Category")}
                        onClick={this.handleClick as any}
                        baseClass={ButtonBaseClass.CUSTOM}
                        className="siteNavNode-toggle"
                    >
                        {this.state.open ? downTriangle(t("Expand")) : rightTriangle(t("Collapse"))}
                    </Button>
                )}
                {!hasChildren && (
                    <span
                        className="siteNavNode-spacer"
                        aria-hidden={true}
                        dangerouslySetInnerHTML={{ __html: space }}
                    />
                )}
                <div className={classNames("siteNavNode-contents")}>
                    <Link className={classNames("siteNavNode-link")} tabIndex={0} to={this.props.url}>
                        <span className="siteNavNode-label">{this.props.name}</span>
                    </Link>
                    {hasChildren && (
                        <ul className={classNames("siteNavNode-children", { isHidden: !this.state.open })} role="group">
                            {childrenContents}
                        </ul>
                    )}
                </div>
            </li>
        );
    }
}
