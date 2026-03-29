import{c as S,a as _,j as s}from"./utils-q4EKThuO.js";import{B as w}from"./badge-DghL8o_I.js";import{C}from"./currency-CQP0FS4i.js";import{j as L,i as k}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const y=P=>{const e=S.c(29),{receaveable:t,className:b,onClick:a,selected:E}=P,f=E&&"bg-accent",g=a&&"cursor-pointer";let r;e[0]!==b||e[1]!==f||e[2]!==g?(r=_("flex items-center justify-between gap-3 px-3 py-2 rounded-md","hover:bg-accent transition-colors",f,g,b),e[0]=b,e[1]=f,e[2]=g,e[3]=r):r=e[3];const j=a?"button":void 0,R=a?0:void 0;let o;e[4]!==a?(o=a?I=>I.key==="Enter"&&a():void 0,e[4]=a,e[5]=o):o=e[5];const h=t.transaction?.tr_number;let c;e[6]!==h?(c=s.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:h}),e[6]=h,e[7]=c):c=e[7];let n;e[8]!==t.amount?(n=s.jsx("span",{className:"text-sm font-semibold",children:s.jsx(C,{value:t.amount})}),e[8]=t.amount,e[9]=n):n=e[9];let l;e[10]!==t.patient?(l=t.patient?.name&&s.jsxs("span",{className:"text-xs text-muted-foreground truncate",children:["-> ",t.patient.name]}),e[10]=t.patient,e[11]=l):l=e[11];let i;e[12]!==l||e[13]!==n?(i=s.jsxs("div",{className:"flex items-baseline gap-1.5",children:[n,l]}),e[12]=l,e[13]=n,e[14]=i):i=e[14];let m;e[15]!==i||e[16]!==c?(m=s.jsxs("div",{className:"min-w-0",children:[c,i]}),e[15]=i,e[16]=c,e[17]=m):m=e[17];const N=t.status==="paid"?"default":"secondary";let d;e[18]!==t.status||e[19]!==N?(d=s.jsx(w,{variant:N,className:"text-xs shrink-0",children:t.status}),e[18]=t.status,e[19]=N,e[20]=d):d=e[20];let p;return e[21]!==a||e[22]!==m||e[23]!==d||e[24]!==r||e[25]!==j||e[26]!==R||e[27]!==o?(p=s.jsxs("div",{className:r,onClick:a,role:j,tabIndex:R,onKeyDown:o,children:[m,d]}),e[21]=a,e[22]=m,e[23]=d,e[24]=r,e[25]=j,e[26]=R,e[27]=o,e[28]=p):p=e[28],p};y.__docgenInfo={description:"",methods:[],displayName:"ReceaveableListItem"};const A={title:"Elements/Receivable/ReceaveableListItem",component:y,tags:["autodocs"],parameters:{layout:"padded"}},u={args:{receaveable:k}},x={args:{receaveable:L}},v={args:{receaveable:k,selected:!0}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    receaveable: mockReceaveable
  }
}`,...u.parameters?.docs?.source}}};x.parameters={...x.parameters,docs:{...x.parameters?.docs,source:{originalSource:`{
  args: {
    receaveable: mockReceaveablePaid
  }
}`,...x.parameters?.docs?.source}}};v.parameters={...v.parameters,docs:{...v.parameters?.docs,source:{originalSource:`{
  args: {
    receaveable: mockReceaveable,
    selected: true
  }
}`,...v.parameters?.docs?.source}}};const F=["Pending","Paid","Selected"];export{x as Paid,u as Pending,v as Selected,F as __namedExportsOrder,A as default};
