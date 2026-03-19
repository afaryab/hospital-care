import{c as x,j as l}from"./utils-q4EKThuO.js";import{S as j,a as T,b as V,c as _,d as I}from"./select-CeMtXFSD.js";import{r as D}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-DZFZ2dbR.js";import"./index-BjvnQY5g.js";const g=a=>{const e=x.c(13),{options:i,value:d,placeholder:b,onValueChange:u,disabled:h,searchable:S}=a,p=b===void 0?"Select transaction":b,m=h===void 0?!1:h,f=S===void 0?!0:S;let t;e[0]!==p?(t=l.jsx(T,{children:l.jsx(V,{placeholder:p})}),e[0]=p,e[1]=t):t=e[1];let s;e[2]!==i?(s=i.map(E),e[2]=i,e[3]=s):s=e[3];let r;e[4]!==f||e[5]!==s?(r=l.jsx(_,{searchable:f,searchPlaceholder:"Search transactions...",children:s}),e[4]=f,e[5]=s,e[6]=r):r=e[6];let o;return e[7]!==m||e[8]!==u||e[9]!==t||e[10]!==r||e[11]!==d?(o=l.jsxs(I,{value:d,onValueChange:u,disabled:m,children:[t,r]}),e[7]=m,e[8]=u,e[9]=t,e[10]=r,e[11]=d,e[12]=o):o=e[12],o};g.__docgenInfo={description:"",methods:[],displayName:"TransactionSelectInput",props:{placeholder:{defaultValue:{value:"'Select transaction'",computed:!1},required:!1},disabled:{defaultValue:{value:"false",computed:!1},required:!1},searchable:{defaultValue:{value:"true",computed:!1},required:!1}}};function E(a){return l.jsx(j,{value:a.value,textValue:a.label,children:a.label},a.value)}const v=D.map(a=>({value:a.id.toString(),label:`${a.tr_number} — PKR ${a.amount}`,transaction:a})),K={title:"Elements/Transaction/TransactionSelectInput",component:g,tags:["autodocs"],parameters:{layout:"centered"}},c={args:{options:v}},n={args:{options:v,disabled:!0}};c.parameters={...c.parameters,docs:{...c.parameters?.docs,source:{originalSource:`{
  args: {
    options
  }
}`,...c.parameters?.docs?.source}}};n.parameters={...n.parameters,docs:{...n.parameters?.docs,source:{originalSource:`{
  args: {
    options,
    disabled: true
  }
}`,...n.parameters?.docs?.source}}};const N=["Default","Disabled"];export{c as Default,n as Disabled,N as __namedExportsOrder,K as default};
